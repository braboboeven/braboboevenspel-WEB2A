<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SpelSessie;
use Illuminate\Http\JsonResponse;

class SpelController extends Controller
{
    public function status(): JsonResponse
    {
        $sessie = SpelSessie::query()->latest()->first();

        if (! $sessie) {
            return response()->json(['data' => null]);
        }

        return response()->json([
            'data' => [
                'id' => $sessie->id,
                'status' => $sessie->status,
                'started_at' => optional($sessie->started_at)->toDateTimeString(),
                'paused_at' => optional($sessie->paused_at)->toDateTimeString(),
                'ended_at' => optional($sessie->ended_at)->toDateTimeString(),
                'total_paused_seconds' => $sessie->total_paused_seconds,
                'elapsed_seconds' => $sessie->elapsedSeconds(),
                'elapsed_formatted' => $sessie->elapsedFormatted(),
                'winner_group_name' => $sessie->winner_group_name,
                'winner_total_score' => $sessie->winner_total_score,
                'winner_declared_at' => optional($sessie->winner_declared_at)->toDateTimeString(),
            ],
        ]);
    }
}
