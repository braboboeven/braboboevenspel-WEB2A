<?php

use App\Models\Verdachte;
use Illuminate\Support\Facades\DB;

it('exposes the legacy verdachten view', function () {
    $verdachte = Verdachte::factory()->create();

    $rows = DB::select('SELECT * FROM verdachten WHERE verdachte_id = ?', [$verdachte->verdachte_id]);

    expect($rows)->toHaveCount(1);
    expect((int) $rows[0]->verdachte_id)->toBe($verdachte->verdachte_id);
});
