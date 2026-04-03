<?php

use App\Models\Groep;
use App\Models\GroepScore;
use App\Models\Opdracht;
use App\Models\User;

it('submits a query via api', function () {
    $user = User::factory()->create();
    $groep = Groep::create([
        'naam' => 'Team Gamma',
        'klas' => '3C',
        'code' => 'IJKLMNOP',
    ]);
    $groep->gebruikers()->attach($user->id, ['is_leider' => true]);

    $opdracht = Opdracht::create([
        'code' => 'T1.1',
        'titel' => 'Test',
        'prompt' => 'Test prompt',
        'correct_query' => 'SELECT 1 AS value',
        'source_table' => 'Verdachte',
        'is_big_boss' => false,
        'reward_correct' => 1000,
        'reward_bad_format' => 500,
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/opdrachten/submit', [
        'opdracht_id' => $opdracht->id,
        'query' => 'SELECT 1 AS value',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.is_correct', true);
    $response->assertJsonPath('data.earned', 500);
});

it('caps big boss score at 10000 via api submissions', function () {
    $user = User::factory()->create();
    $groep = Groep::create([
        'naam' => 'Team Boss',
        'klas' => '4C',
        'code' => 'BOSS0001',
    ]);
    $groep->gebruikers()->attach($user->id, ['is_leider' => true]);

    GroepScore::create([
        'groep_id' => $groep->id,
        'score' => 0,
        'big_boss_score' => 9800,
    ]);

    $opdracht = Opdracht::create([
        'code' => 'BB-1',
        'titel' => 'Big Boss',
        'prompt' => 'Vind de big boss',
        'correct_query' => 'SELECT 1',
        'source_table' => 'Verdachte',
        'is_big_boss' => true,
        'reward_correct' => 1000,
        'reward_bad_format' => 500,
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/opdrachten/submit', [
        'opdracht_id' => $opdracht->id,
        'query' => 'SELECT 1',
    ]);

    $response->assertOk();

    $groepScore = GroepScore::query()->where('groep_id', $groep->id)->first();

    expect($groepScore)->not->toBeNull();
    expect($groepScore->big_boss_score)->toBe(10000);
});
