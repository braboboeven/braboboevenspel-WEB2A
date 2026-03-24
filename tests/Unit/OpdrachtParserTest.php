<?php

use App\Actions\Game\OpdrachtParser;

it('parses opdrachten from sql comments and queries', function () {
    $contents = <<<'SQL'
-- 1.1
-- Vind alle verdachten die vrouw zijn
SELECT *
FROM Verdachte
WHERE geslacht = 'vrouw';
SQL;

    $parser = new OpdrachtParser();
    $opdrachten = $parser->parseFromString($contents, 'Verdachte', 'V');

    expect($opdrachten)->toHaveCount(1);
    expect($opdrachten[0]['code'])->toBe('V1.1');
    expect($opdrachten[0]['prompt'])->toBe('Vind alle verdachten die vrouw zijn');
    expect($opdrachten[0]['correct_query'])->toContain("FROM Verdachte");
});
