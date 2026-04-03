<?php

use App\Models\SpelSessie;
use App\Models\User;
use Illuminate\Support\Facades\Date;

test('docent timer shows elapsed time for running session', function () {
    Date::setTestNow(Date::parse('2026-03-27 12:00:00'));

    $docent = User::factory()->create([
        'is_docent' => true,
    ]);

    SpelSessie::create([
        'status' => 'running',
        'started_at' => Date::parse('2026-03-27 11:58:40'),
        'total_paused_seconds' => 0,
        'created_by_user_id' => $docent->id,
    ]);

    $response = $this->actingAs($docent)->get(route('docent'));

    $response->assertOk();
    $response->assertSee('01:20');

    Date::setTestNow();
});
