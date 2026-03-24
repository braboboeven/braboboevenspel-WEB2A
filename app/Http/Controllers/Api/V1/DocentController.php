<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendHintRequest;
use App\Models\BigBossHint;
use App\Models\Hint;
use App\Models\HintVerzending;
use App\Models\SpelSessie;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DocentController extends Controller
{
    public function startSpel(): JsonResponse
    {
        $this->ensureDocent();

        $sessie = SpelSessie::query()->create([
            'status' => 'running',
            'started_at' => now(),
            'created_by_user_id' => Auth::id(),
        ]);

        return response()->json(['data' => ['id' => $sessie->id, 'status' => $sessie->status]]);
    }

    public function stopSpel(): JsonResponse
    {
        $this->ensureDocent();

        $sessie = SpelSessie::query()->latest()->first();

        if (! $sessie) {
            return response()->json(['message' => 'Geen sessie gevonden.'], 404);
        }

        $sessie->update([
            'status' => 'stopped',
            'ended_at' => now(),
        ]);

        return response()->json(['data' => ['id' => $sessie->id, 'status' => $sessie->status]]);
    }

    public function sendHint(SendHintRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $type = $validated['type'];
        $hintId = $validated['hint_id'];
        $broadcast = (bool) ($validated['broadcast'] ?? true);
        $groepIds = $validated['groep_ids'] ?? [];

        if ($type === 'normal' && ! Hint::query()->where('hint_nummer', $hintId)->exists()) {
            return response()->json(['message' => 'Hint niet gevonden.'], 404);
        }

        if ($type === 'bigboss' && ! BigBossHint::query()->where('id', $hintId)->exists()) {
            return response()->json(['message' => 'Big Boss hint niet gevonden.'], 404);
        }

        if (! $broadcast && $groepIds === []) {
            return response()->json(['message' => 'Selecteer minstens een groep of kies broadcast.'], 422);
        }

        $payload = [
            'hint_nummer' => $type === 'normal' ? $hintId : null,
            'big_boss_hint_id' => $type === 'bigboss' ? $hintId : null,
            'sent_by_user_id' => Auth::id(),
            'sent_at' => now(),
        ];

        if ($broadcast) {
            HintVerzending::create($payload);
        } else {
            foreach ($groepIds as $groepId) {
                HintVerzending::create($payload + ['groep_id' => $groepId]);
            }
        }

        return response()->json(['message' => 'Hint verstuurd.']);
    }

    private function ensureDocent(): void
    {
        if (! Auth::user()?->is_docent) {
            abort(403);
        }
    }
}
