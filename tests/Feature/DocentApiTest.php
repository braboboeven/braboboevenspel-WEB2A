<?php

use App\Models\BigBossHint;
use App\Models\Groep;
use App\Models\Hint;
use App\Models\User;

it('starts and stops a session via docent api', function () {
    $docent = User::factory()->create(['is_docent' => true]);

    $startResponse = $this->actingAs($docent)->postJson('/api/v1/docent/spel/start');
    $startResponse->assertOk();
    $startResponse->assertJsonPath('data.status', 'running');

    $stopResponse = $this->actingAs($docent)->postJson('/api/v1/docent/spel/stop');
    $stopResponse->assertOk();
    $stopResponse->assertJsonPath('data.status', 'stopped');
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
