<?php

use App\Models\SpelSessie;
use App\Models\User;
use Illuminate\Support\Facades\Date;

it('returns null when no session exists', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/spel/sessie');

    $response->assertOk();
    $response->assertJson(['data' => null]);
});

it('returns elapsed time for the latest session', function () {
    Date::setTestNow(Date::parse('2026-03-27 12:00:00'));

    $user = User::factory()->create();
    $sessie = SpelSessie::create([
        'status' => 'running',
        'started_at' => Date::parse('2026-03-27 11:58:30'),
        'total_paused_seconds' => 10,
        'created_by_user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/spel/sessie');

    $response->assertOk();
    $response->assertJsonPath('data.id', $sessie->id);
    $response->assertJsonPath('data.elapsed_seconds', 80);
    $response->assertJsonPath('data.elapsed_formatted', '01:20');

    Date::setTestNow();
});
