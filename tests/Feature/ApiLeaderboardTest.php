<?php

use App\Models\Groep;
use App\Models\GroepScore;
use App\Models\User;

it('returns leaderboard entries via api', function () {
    $user = User::factory()->create();
    $groep = Groep::create([
        'naam' => 'Team Omega',
        'klas' => '6F',
        'code' => 'YZABCDEF',
    ]);

    GroepScore::create([
        'groep_id' => $groep->id,
        'score' => 1200,
        'big_boss_score' => 500,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/leaderboard');

    $response->assertOk();
    $response->assertJsonPath('data.0.groep.naam', 'Team Omega');
});

it('returns big boss leaderboard entries via api', function () {
    $user = User::factory()->create();
    $groep = Groep::create([
        'naam' => 'Team Sigma',
        'klas' => '5C',
        'code' => 'SIGMA123',
    ]);

    GroepScore::create([
        'groep_id' => $groep->id,
        'score' => 600,
        'big_boss_score' => 9000,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/leaderboard/big-boss');

    $response->assertOk();
    $response->assertJsonPath('data.0.groep.naam', 'Team Sigma');
});
