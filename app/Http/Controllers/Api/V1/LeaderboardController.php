<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GroepScore;
use Illuminate\Http\JsonResponse;

class LeaderboardController extends Controller
{
    public function index(): JsonResponse
    {
        $scores = GroepScore::query()
            ->with('groep')
            ->orderByDesc('score')
            ->get()
            ->map(fn (GroepScore $score) => [
                'groep' => [
                    'id' => $score->groep?->id,
                    'naam' => $score->groep?->naam,
                    'klas' => $score->groep?->klas,
                    'code' => $score->groep?->code,
                ],
                'score' => $score->score,
                'big_boss_score' => $score->big_boss_score,
                'last_submission_at' => $score->last_submission_at?->toDateTimeString(),
            ]);

        return response()->json(['data' => $scores]);
    }
}
