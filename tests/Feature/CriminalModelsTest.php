<?php

use App\Models\Gebruiker;
use App\Models\Hint;
use App\Models\Misdaad;
use App\Models\Verdachte;

it('creates a gebruiker record', function () {
    $gebruiker = Gebruiker::factory()->create();

    expect($gebruiker->getKey())->not->toBeNull();
});

it('creates a hint record with a custom key', function () {
    $hint = Hint::factory()->create();

    expect($hint->getKey())->toBe($hint->hint_nummer);
});

it('links misdaad to verdachte', function () {
    $verdachte = Verdachte::factory()->create();
    $misdaad = Misdaad::factory()->create(['verdachte_id' => $verdachte->verdachte_id]);

    expect($misdaad->verdachte->is($verdachte))->toBeTrue();
    expect($verdachte->misdaden->contains($misdaad))->toBeTrue();
});
