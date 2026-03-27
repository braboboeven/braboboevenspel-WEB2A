<?php

use App\Models\Opdracht;
use Database\Seeders\GameDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds big boss opdrachten with correct rewards', function () {
    $this->seed(GameDataSeeder::class);

    $opdracht = Opdracht::query()->where('is_big_boss', true)->first();

    expect($opdracht)->not->toBeNull();
    expect($opdracht?->reward_correct)->toBe(10000);
});
