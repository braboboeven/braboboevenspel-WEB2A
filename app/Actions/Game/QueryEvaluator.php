<?php

namespace App\Actions\Game;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class QueryEvaluator
{
    /**
     * @return array<string, mixed>
     */
    public function evaluate(string $submittedQuery, string $correctQuery, int $rewardCorrect, int $rewardBadFormat): array
    {
        $submittedQuery = $this->normalizeQuery($submittedQuery);
        $correctQuery = $this->normalizeQuery($correctQuery);

        if (! $this->isSafeSelect($submittedQuery)) {
            return [
                'is_safe' => false,
                'is_correct' => false,
                'is_good_format' => false,
                'earned' => 0,
                'error' => 'Alleen SELECT-queries zijn toegestaan.',
                'submitted_count' => 0,
                'correct_count' => 0,
            ];
        }

        try {
            $submittedRows = DB::select($submittedQuery);
        } catch (Throwable $exception) {
            return [
                'is_safe' => true,
                'is_correct' => false,
                'is_good_format' => false,
                'earned' => 0,
                'error' => $exception->getMessage(),
                'submitted_count' => 0,
                'correct_count' => 0,
            ];
        }

        $correctRows = DB::select($correctQuery);
        $isCorrect = $this->resultsMatch($submittedRows, $correctRows);
        $isGoodFormat = $this->hasGoodFormat($submittedQuery);

        $earned = 0;
        if ($isCorrect) {
            $earned = $isGoodFormat ? $rewardCorrect : $rewardBadFormat;
        }

        return [
            'is_safe' => true,
            'is_correct' => $isCorrect,
            'is_good_format' => $isGoodFormat,
            'earned' => $earned,
            'error' => null,
            'submitted_count' => count($submittedRows),
            'correct_count' => count($correctRows),
        ];
    }

    public function isSafeSelect(string $query): bool
    {
        $normalized = Str::lower($query);

        if (! Str::startsWith(ltrim($normalized), 'select')) {
            return false;
        }

        if (Str::contains($normalized, [';', ' insert ', ' update ', ' delete ', ' drop ', ' alter ', ' truncate ', ' create '])) {
            return false;
        }

        return true;
    }

    private function normalizeQuery(string $query): string
    {
        $query = trim($query);

        return rtrim($query, "; \t\n\r\0\x0B");
    }

    /**
     * @param  array<int, object>  $submittedRows
     * @param  array<int, object>  $correctRows
     */
    public function resultsMatch(array $submittedRows, array $correctRows): bool
    {
        return $this->normalizeRows($submittedRows) === $this->normalizeRows($correctRows);
    }

    public function hasGoodFormat(string $query): bool
    {
        if (! Str::contains($query, "\n")) {
            return false;
        }

        $keywords = ['SELECT', 'FROM', 'WHERE', 'ORDER BY', 'GROUP BY'];
        foreach ($keywords as $keyword) {
            if (Str::contains($query, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, object>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function normalizeRows(array $rows): array
    {
        return array_map(function (object $row): array {
            return Arr::sortRecursive((array) $row);
        }, $rows);
    }
}
