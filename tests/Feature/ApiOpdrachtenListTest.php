<?php

use App\Models\Opdracht;
use App\Models\User;

it('lists opdrachten via api', function () {
    $user = User::factory()->create();

    Opdracht::create([
        'code' => 'T2.1',
        'titel' => 'Test 2',
        'prompt' => 'Prompt',
        'correct_query' => 'SELECT 1',
        'source_table' => 'Verdachte',
        'is_big_boss' => false,
        'reward_correct' => 1000,
        'reward_bad_format' => 500,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/opdrachten');

    $response->assertOk();
    $response->assertJsonPath('data.0.code', 'T2.1');
});

it('filters big boss opdrachten via api', function () {
    $user = User::factory()->create();

    Opdracht::create([
        'code' => 'B1.1',
        'titel' => 'Big Boss',
        'prompt' => 'Big Boss prompt',
        'correct_query' => 'SELECT 1',
        'source_table' => 'Misdaad',
        'is_big_boss' => true,
        'reward_correct' => 10000,
        'reward_bad_format' => 500,
    ]);

    Opdracht::create([
        'code' => 'T3.1',
        'titel' => 'Normaal',
        'prompt' => 'Prompt',
        'correct_query' => 'SELECT 1',
        'source_table' => 'Verdachte',
        'is_big_boss' => false,
        'reward_correct' => 1000,
        'reward_bad_format' => 500,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/opdrachten?big_boss=true');

    $response->assertOk();
    $response->assertJsonPath('data.0.code', 'B1.1');
});
