<?php

use App\Models\Opdracht;
use App\Models\User;

it('lists opdrachten via api', function () {
    $user = User::factory()->create();

    Opdracht::create([
        'code' => 'T2.1',
        'titel' => 'Test 2',
        'prompt' => 'Prompt',
        'correct_query' => "SELECT 1",
        'source_table' => 'Verdachte',
        'is_big_boss' => false,
        'reward_correct' => 1000,
        'reward_bad_format' => 500,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/opdrachten');

    $response->assertOk();
    $response->assertJsonPath('data.0.code', 'T2.1');
});
