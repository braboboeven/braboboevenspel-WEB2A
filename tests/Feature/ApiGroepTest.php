<?php

use App\Models\Groep;
use App\Models\User;

it('creates a group via api', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/groepen', [
        'naam' => 'Team Alpha',
        'klas' => '1A',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.naam', 'Team Alpha');

    expect(Groep::query()->where('naam', 'Team Alpha')->exists())->toBeTrue();
});

it('joins a group via api', function () {
    $user = User::factory()->create();
    $groep = Groep::create([
        'naam' => 'Team Beta',
        'klas' => '2B',
        'code' => 'ABCDEFGH',
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/groepen/join', [
        'code' => 'ABCDEFGH',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.code', 'ABCDEFGH');
});
