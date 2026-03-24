<?php

use App\Actions\Game\QueryEvaluator;
use Illuminate\Support\Facades\DB;

it('rejects non-select queries', function () {
    $evaluator = app(QueryEvaluator::class);

    $result = $evaluator->evaluate(
        'DELETE FROM Verdachte',
        "SELECT naam FROM Verdachte WHERE verdachte_id = 1",
        1000,
        500
    );

    expect($result['is_safe'])->toBeFalse();
    expect($result['earned'])->toBe(0);
});

it('scores correct queries with format bonus', function () {
    DB::table('Verdachte')->insert([
        'verdachte_id' => 1,
        'naam' => 'Test Verdachte',
        'geslacht' => 'vrouw',
        'leeftijd' => 30,
        'lengte' => 'klein',
        'haarkleur' => 'blond',
        'kleur_ogen' => 'blauw',
        'gezichtsbeharing' => 0,
        'tatoeages' => 0,
        'bril' => 'nee',
        'littekens' => null,
        'schoenmaat' => 'groot',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $evaluator = app(QueryEvaluator::class);

    $result = $evaluator->evaluate(
        "SELECT naam\nFROM Verdachte\nWHERE verdachte_id = 1",
        "SELECT naam\nFROM Verdachte\nWHERE verdachte_id = 1",
        1000,
        500
    );

    expect($result['is_correct'])->toBeTrue();
    expect($result['is_good_format'])->toBeTrue();
    expect($result['earned'])->toBe(1000);
});

it('scores correct queries without format bonus', function () {
    $evaluator = app(QueryEvaluator::class);

    $result = $evaluator->evaluate(
        'select naam from Verdachte where verdachte_id = 1',
        'SELECT naam FROM Verdachte WHERE verdachte_id = 1',
        1000,
        500
    );

    expect($result['is_correct'])->toBeTrue();
    expect($result['is_good_format'])->toBeFalse();
    expect($result['earned'])->toBe(500);
});
