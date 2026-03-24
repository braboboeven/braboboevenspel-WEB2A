<?php

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
