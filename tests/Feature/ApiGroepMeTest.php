<?php

use App\Models\Groep;
use App\Models\User;

it('returns current group via api', function () {
    $user = User::factory()->create();
    $groep = Groep::create([
        'naam' => 'Team Sigma',
        'klas' => '7G',
        'code' => 'SIGMAAAA',
    ]);
    $groep->gebruikers()->attach($user->id, ['is_leider' => true]);

    $response = $this->actingAs($user)->getJson('/api/v1/groepen/me');

    $response->assertOk();
    $response->assertJsonPath('data.naam', 'Team Sigma');
});
