<?php

use App\Models\Groep;
use App\Models\Hint;
use App\Models\HintVerzending;
use App\Models\User;

it('lists hints for a group via api', function () {
    $user = User::factory()->create();
    $groep = Groep::create([
        'naam' => 'Team Delta',
        'klas' => '5E',
        'code' => 'QRSTUVWX',
    ]);
    $groep->gebruikers()->attach($user->id, ['is_leider' => true]);

    Hint::create([
        'hint_nummer' => 888,
        'hint_beschrijving' => 'Hint tekst',
        'aantal_rows' => 10,
    ]);

    HintVerzending::create([
        'groep_id' => $groep->id,
        'hint_nummer' => 888,
        'sent_by_user_id' => $user->id,
        'sent_at' => now(),
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/hints');

    $response->assertOk();
    $response->assertJsonPath('data.0.hint_nummer', 888);
});
