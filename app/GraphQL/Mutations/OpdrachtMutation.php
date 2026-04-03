<?php

namespace App\GraphQL\Mutations;

use App\Actions\Game\QueryEvaluator;
use App\Models\Groep;
use App\Models\GroepScore;
use App\Models\Opdracht;
use App\Models\Poging;
use Illuminate\Support\Facades\Auth;

class OpdrachtMutation
{
    /**
     * @return array<string, mixed>
     */
    public function submit(?object $root, array $args, QueryEvaluator $evaluator): array
    {
        $groep = Auth::user()?->groepen()->first();
        if (! $groep) {
            abort(422, 'Je moet eerst aan een groep gekoppeld zijn.');
        }

        $opdracht = Opdracht::query()
            ->with('bigBossQuery')
            ->findOrFail($args['opdracht_id']);

        $correctQuery = $opdracht->resolvedCorrectQuery();
        $rewardCorrect = $opdracht->resolvedRewardCorrect();

        $result = $evaluator->evaluate(
            $args['query'],
            $correctQuery,
            $rewardCorrect,
            $opdracht->reward_bad_format
        );

        if (! $result['is_safe']) {
            abort(422, (string) $result['error']);
        }

        if ($result['error']) {
            abort(422, (string) $result['error']);
        }

        $earned = (int) $result['earned'];
        if ($opdracht->is_big_boss && $result['is_correct'] && ! $result['is_good_format']) {
            $earned = max(0, $rewardCorrect - $opdracht->resolvedBigBossBadFormatPenalty());
        }

        if (! $opdracht->is_big_boss && $opdracht->verdachte_nummer) {
            $currentTotal = Poging::query()
                ->where('groep_id', $groep->id)
                ->whereHas('opdracht', fn ($query) => $query->where('verdachte_nummer', $opdracht->verdachte_nummer))
                ->sum('earned');

            $remaining = max(0, 5000 - $currentTotal);
            $earned = min($earned, $remaining);
        }

        Poging::create([
            'groep_id' => $groep->id,
            'opdracht_id' => $opdracht->id,
            'user_id' => Auth::id(),
            'submitted_query' => $args['query'],
            'is_correct' => $result['is_correct'],
            'is_good_format' => $result['is_good_format'],
            'earned' => $earned,
            'submitted_at' => now(),
        ]);

        $this->updateScore($groep, $opdracht, $earned);

        return [
            'is_correct' => $result['is_correct'],
            'is_good_format' => $result['is_good_format'],
            'earned' => $earned,
            'submitted_count' => $result['submitted_count'],
            'correct_count' => $result['correct_count'],
            'correct_query' => $correctQuery,
        ];
    }

    private function updateScore(Groep $groep, Opdracht $opdracht, int $earned): void
    {
        $score = GroepScore::query()->firstOrCreate(
            ['groep_id' => $groep->id],
            ['score' => 0, 'big_boss_score' => 0]
        );

        if ($opdracht->is_big_boss) {
            $score->big_boss_score = min(10000, $score->big_boss_score + $earned);
        } else {
            $score->score += $earned;
        }

        $score->last_submission_at = now();
        $score->save();
    }
}
