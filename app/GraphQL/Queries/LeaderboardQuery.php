<?php

namespace App\GraphQL\Queries;

use App\Models\GroepScore;

class LeaderboardQuery
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return GroepScore::query()
            ->with('groep')
            ->orderByDesc('score')
            ->get()
            ->map(fn (GroepScore $score) => [
                'groep' => $score->groep,
                'score' => $score->score,
                'big_boss_score' => $score->big_boss_score,
                'last_submission_at' => $score->last_submission_at?->toDateTimeString(),
            ])
            ->all();
    }
}
