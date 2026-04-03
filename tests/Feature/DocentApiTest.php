<?php

use App\Models\BigBossHint;
use App\Models\Groep;
use App\Models\GroepScore;
use App\Models\Hint;
use App\Models\HintVerzending;
use App\Models\Opdracht;
use App\Models\Poging;
use App\Models\SpelSessie;
use App\Models\User;
use Illuminate\Support\Facades\Date;

it('starts and stops a session via docent api', function () {
    $docent = User::factory()->create(['is_docent' => true]);

    $startResponse = $this->actingAs($docent)->postJson('/api/v1/docent/spel/start');
    $startResponse->assertOk();
    $startResponse->assertJsonPath('data.status', 'running');

    $stopResponse = $this->actingAs($docent)->postJson('/api/v1/docent/spel/stop');
    $stopResponse->assertOk();
    $stopResponse->assertJsonPath('data.status', 'stopped');
});

it('can pause, resume, and end a session via docent api', function () {
    Date::setTestNow(Date::parse('2026-04-03 10:00:00'));

    $docent = User::factory()->create(['is_docent' => true]);

    $groep = Groep::create([
        'naam' => 'Tijdelijke groep',
        'klas' => '6A',
        'code' => 'CLEAN001',
    ]);

    $groep->gebruikers()->attach($docent->id, ['is_leider' => true]);

    $opdracht = Opdracht::create([
        'code' => 'QX-1',
        'titel' => 'Cleanup test',
        'prompt' => 'Test prompt',
        'correct_query' => 'SELECT 1',
        'source_table' => 'Verdachte',
        'reward_correct' => 1000,
        'reward_bad_format' => 500,
    ]);

    GroepScore::create([
        'groep_id' => $groep->id,
        'score' => 1000,
        'big_boss_score' => 0,
    ]);

    Poging::create([
        'groep_id' => $groep->id,
        'opdracht_id' => $opdracht->id,
        'user_id' => $docent->id,
        'submitted_query' => 'SELECT * FROM Verdachte',
        'is_correct' => true,
        'is_good_format' => true,
        'earned' => 1000,
        'submitted_at' => now(),
    ]);

    HintVerzending::create([
        'groep_id' => $groep->id,
        'sent_by_user_id' => $docent->id,
        'sent_at' => now(),
    ]);

    $this->actingAs($docent)->postJson('/api/v1/docent/spel/start')->assertOk();

    Date::setTestNow(Date::parse('2026-04-03 10:05:00'));
    $pauseResponse = $this->actingAs($docent)->postJson('/api/v1/docent/spel/pause');
    $pauseResponse->assertOk();
    $pauseResponse->assertJsonPath('data.status', 'paused');

    Date::setTestNow(Date::parse('2026-04-03 10:08:00'));
    $resumeResponse = $this->actingAs($docent)->postJson('/api/v1/docent/spel/resume');
    $resumeResponse->assertOk();
    $resumeResponse->assertJsonPath('data.status', 'running');

    $sessie = SpelSessie::query()->latest()->first();

    expect($sessie)->not->toBeNull();
    expect($sessie->total_paused_seconds)->toBe(180);

    Date::setTestNow(Date::parse('2026-04-03 10:10:00'));
    $endResponse = $this->actingAs($docent)->postJson('/api/v1/docent/spel/end');
    $endResponse->assertOk();
    $endResponse->assertJsonPath('data.status', 'stopped');

    expect(Groep::query()->count())->toBe(0);
    expect(GroepScore::query()->count())->toBe(0);
    expect(Poging::query()->count())->toBe(0);
    expect(HintVerzending::query()->count())->toBe(0);

    Date::setTestNow();
});

it('returns docent hint options and groepen', function () {
    $docent = User::factory()->create(['is_docent' => true]);

    Hint::create([
        'hint_nummer' => 10,
        'hint_beschrijving' => 'Hint optie',
        'aantal_rows' => 1,
    ]);

    BigBossHint::create([
        'nummer' => 1,
        'beschrijving' => 'Big Boss optie',
        'lesnummer' => 1,
    ]);

    Groep::create([
        'naam' => 'Groep A',
        'klas' => '4A',
        'code' => 'GROEP001',
    ]);

    $optionsResponse = $this->actingAs($docent)->getJson('/api/v1/docent/hints/options');
    $optionsResponse->assertOk();
    $optionsResponse->assertJsonPath('data.hints.0.hint_nummer', 10);
    $optionsResponse->assertJsonPath('data.big_boss_hints.0.nummer', 1);

    $groepenResponse = $this->actingAs($docent)->getJson('/api/v1/docent/groepen');
    $groepenResponse->assertOk();
    $groepenResponse->assertJsonPath('data.0.code', 'GROEP001');
});
