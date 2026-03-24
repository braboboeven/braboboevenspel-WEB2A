<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Game\QueryEvaluator;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitQueryRequest;
use App\Models\Groep;
use App\Models\GroepScore;
use App\Models\Opdracht;
use App\Models\Poging;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpdrachtController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Opdracht::query()->orderBy('code');

        if ($request->has('big_boss')) {
            $query->where('is_big_boss', $request->boolean('big_boss'));
        }

        $opdrachten = $query->get()->map(fn (Opdracht $opdracht) => [
            'id' => $opdracht->id,
            'code' => $opdracht->code,
            'titel' => $opdracht->titel,
            'prompt' => $opdracht->prompt,
            'source_table' => $opdracht->source_table,
            'verdachte_nummer' => $opdracht->verdachte_nummer,
            'step_nummer' => $opdracht->step_nummer,
            'is_big_boss' => $opdracht->is_big_boss,
            'reward_correct' => $opdracht->reward_correct,
            'reward_bad_format' => $opdracht->reward_bad_format,
        ]);

        return response()->json(['data' => $opdrachten]);
    }

    public function submit(SubmitQueryRequest $request, QueryEvaluator $evaluator): JsonResponse
    {
        $groep = Auth::user()?->groepen()->first();
        if (! $groep) {
            return response()->json(['message' => 'Je moet eerst aan een groep gekoppeld zijn.'], 422);
        }

        $validated = $request->validated();
        $opdracht = Opdracht::query()->findOrFail($validated['opdracht_id']);

        $result = $evaluator->evaluate(
            $validated['query'],
            $opdracht->correct_query,
            $opdracht->reward_correct,
            $opdracht->reward_bad_format
        );

        if (! $result['is_safe']) {
            return response()->json(['message' => $result['error']], 422);
        }

        if ($result['error']) {
            return response()->json(['message' => $result['error']], 422);
        }

        $earned = (int) $result['earned'];
        if ($opdracht->is_big_boss && $result['is_correct'] && ! $result['is_good_format']) {
            $earned = max(0, $opdracht->reward_correct - 500);
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
            'submitted_query' => $validated['query'],
            'is_correct' => $result['is_correct'],
            'is_good_format' => $result['is_good_format'],
            'earned' => $earned,
            'submitted_at' => now(),
        ]);

        $this->updateScore($groep, $opdracht, $earned);

        return response()->json([
            'data' => [
                'is_correct' => $result['is_correct'],
                'is_good_format' => $result['is_good_format'],
                'earned' => $earned,
                'submitted_count' => $result['submitted_count'],
                'correct_count' => $result['correct_count'],
                'correct_query' => $opdracht->correct_query,
            ],
        ]);
    }

    private function updateScore(Groep $groep, Opdracht $opdracht, int $earned): void
    {
        $score = GroepScore::query()->firstOrCreate(
            ['groep_id' => $groep->id],
            ['score' => 0, 'big_boss_score' => 0]
        );

        if ($opdracht->is_big_boss) {
            $score->big_boss_score += $earned;
        } else {
            $score->score += $earned;
        }

        $score->last_submission_at = now();
        $score->save();
    }
}
