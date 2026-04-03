<?php

use App\Models\BigBossHint;
use App\Models\HintVerzending;
use App\Models\SpelSessie;
use App\Models\User;
use Illuminate\Support\Facades\Date;

it('sends the first big boss hint after 24 hours', function () {
    Date::setTestNow(Date::parse('2026-04-03 12:00:00'));

    $user = User::factory()->create(['is_docent' => true]);

    $firstHint = BigBossHint::create([
        'nummer' => 1,
        'beschrijving' => 'Eerste big boss hint',
        'lesnummer' => 1,
    ]);

    BigBossHint::create([
        'nummer' => 2,
        'beschrijving' => 'Tweede big boss hint',
        'lesnummer' => 2,
    ]);

    SpelSessie::create([
        'status' => 'running',
        'started_at' => Date::parse('2026-04-02 11:55:00'),
        'total_paused_seconds' => 0,
        'created_by_user_id' => $user->id,
    ]);

    $this->artisan('spel:send-big-boss-hint')
        ->assertExitCode(0);

    $this->assertDatabaseCount('hint_verzendings', 1);

    $hintVerzending = HintVerzending::query()->first();

    expect($hintVerzending?->big_boss_hint_id)->toBe($firstHint->id);

    Date::setTestNow();
});

it('does not send a big boss hint before 24 hours', function () {
    Date::setTestNow(Date::parse('2026-04-03 12:00:00'));

    $user = User::factory()->create(['is_docent' => true]);

    BigBossHint::create([
        'nummer' => 1,
        'beschrijving' => 'Eerste big boss hint',
        'lesnummer' => 1,
    ]);

    SpelSessie::create([
        'status' => 'running',
        'started_at' => Date::parse('2026-04-02 13:30:00'),
        'total_paused_seconds' => 0,
        'created_by_user_id' => $user->id,
    ]);

    $this->artisan('spel:send-big-boss-hint')
        ->assertExitCode(0);

    $this->assertDatabaseCount('hint_verzendings', 0);

    Date::setTestNow();
});
