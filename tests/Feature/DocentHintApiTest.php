<?php

use App\Models\Hint;
use App\Models\User;

it('sends a hint via docent api', function () {
    $docent = User::factory()->create(['is_docent' => true]);

    Hint::create([
        'hint_nummer' => 999,
        'hint_beschrijving' => 'Test hint',
        'aantal_rows' => 1,
    ]);

    $response = $this->actingAs($docent)->postJson('/api/v1/docent/hints', [
        'type' => 'normal',
        'hint_id' => 999,
        'broadcast' => true,
    ]);

    $response->assertOk();
    $response->assertJsonPath('message', 'Hint verstuurd.');
});
