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

        $endTime = match ($sessie->status) {
            'running' => now(),
            'paused' => $sessie->paused_at ?? now(),
            default => $sessie->ended_at ?? now(),
        };

        $elapsed = $endTime->diffInSeconds($sessie->started_at ?? $endTime) - $sessie->total_paused_seconds;
        $elapsed = max(0, $elapsed);

        $minutes = str_pad((string) intdiv($elapsed, 60), 2, '0', STR_PAD_LEFT);
        $seconds = str_pad((string) ($elapsed % 60), 2, '0', STR_PAD_LEFT);

        return response()->json([
            'data' => [
                'id' => $sessie->id,
                'status' => $sessie->status,
                'started_at' => optional($sessie->started_at)->toDateTimeString(),
                'paused_at' => optional($sessie->paused_at)->toDateTimeString(),
                'ended_at' => optional($sessie->ended_at)->toDateTimeString(),
                'total_paused_seconds' => $sessie->total_paused_seconds,
                'elapsed_seconds' => $elapsed,
                'elapsed_formatted' => $minutes.':'.$seconds,
            ],
        ]);
    }
}
