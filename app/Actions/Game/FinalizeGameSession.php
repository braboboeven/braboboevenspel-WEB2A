<?php

namespace App\Actions\Game;

use App\Models\GroepScore;
use App\Models\SpelSessie;

class FinalizeGameSession
{
    /**
     * @return array{group_name: ?string, total_score: ?int}
     */
    public function __invoke(?SpelSessie $sessie, bool $resetState = true): array
    {
        $winner = GroepScore::query()
            ->with('groep')
            ->orderByRaw('(score + big_boss_score) DESC')
            ->orderByDesc('big_boss_score')
            ->orderByDesc('score')
            ->first();

        $winnerGroupName = $winner?->groep?->naam;
        $winnerTotalScore = $winner
            ? (int) ($winner->score + $winner->big_boss_score)
            : null;

        if ($sessie) {
            $sessie->endGame();
            $sessie->forceFill([
                'winner_group_name' => $winnerGroupName,
                'winner_total_score' => $winnerTotalScore,
                'winner_declared_at' => $winner ? now() : null,
            ])->save();
        }

        if ($resetState) {
            app(ResetGameState::class)();
        }

        return [
            'group_name' => $winnerGroupName,
            'total_score' => $winnerTotalScore,
        ];
    }
}
