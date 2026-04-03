<?php

use App\Models\SpelSessie;
use App\Models\User;
use Illuminate\Support\Facades\Date;

it('adds paused duration when ending a paused game', function () {
    Date::setTestNow(Date::parse('2026-04-03 10:00:00'));

    $user = User::factory()->create();

    $sessie = SpelSessie::create([
        'status' => 'running',
        'started_at' => now(),
        'total_paused_seconds' => 0,
        'created_by_user_id' => $user->id,
    ]);

    Date::setTestNow(Date::parse('2026-04-03 10:10:00'));
    $sessie->pause();

    Date::setTestNow(Date::parse('2026-04-03 10:13:30'));
    $sessie->endGame();

    $sessie->refresh();

    expect($sessie->status)->toBe('stopped');
    expect($sessie->total_paused_seconds)->toBe(210);
    expect($sessie->ended_at)->not->toBeNull();

    Date::setTestNow();
});
