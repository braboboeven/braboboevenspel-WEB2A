<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Game\FinalizeGameSession;
use App\Actions\Game\ManageVerdachteBank;
use App\Http\Controllers\Controller;
use App\Http\Requests\BankVerdachteRequest;
use App\Http\Requests\ConfiscateVerdachteRequest;
use App\Http\Requests\SendHintRequest;
use App\Models\BigBossHint;
use App\Models\Groep;
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

    public function stopSpel(FinalizeGameSession $finalizeGameSession): JsonResponse
    {
        return $this->endSpel($finalizeGameSession);
    }

    public function pauseSpel(): JsonResponse
    {
        $this->ensureDocent();

        $sessie = SpelSessie::query()->latest()->first();

        if (! $sessie) {
            return response()->json(['message' => 'Geen sessie gevonden.'], 404);
        }

        $sessie->pause();

        return response()->json(['data' => ['id' => $sessie->id, 'status' => $sessie->status]]);
    }

    public function resumeSpel(): JsonResponse
    {
        $this->ensureDocent();

        $sessie = SpelSessie::query()->latest()->first();

        if (! $sessie) {
            return response()->json(['message' => 'Geen sessie gevonden.'], 404);
        }

        $sessie->resume();

        return response()->json(['data' => ['id' => $sessie->id, 'status' => $sessie->status]]);
    }

    public function endSpel(FinalizeGameSession $finalizeGameSession): JsonResponse
    {
        $this->ensureDocent();

        $sessie = SpelSessie::query()->latest()->first();
        $winner = $finalizeGameSession($sessie, true);

        return response()->json([
            'data' => [
                'id' => $sessie?->id,
                'status' => 'stopped',
                'winner_group_name' => $winner['group_name'],
                'winner_total_score' => $winner['total_score'],
            ],
        ]);
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

    public function groepen(): JsonResponse
    {
        $this->ensureDocent();

        $groepen = Groep::query()
            ->with(['score', 'verdachteBanken'])
            ->orderBy('naam')
            ->get()
            ->map(fn (Groep $groep) => [
                'id' => $groep->id,
                'naam' => $groep->naam,
                'klas' => $groep->klas,
                'code' => $groep->code,
                'score' => (int) ($groep->score?->score ?? 0),
                'big_boss_score' => (int) ($groep->score?->big_boss_score ?? 0),
                'bank_balance' => (int) $groep->verdachteBanken->sum('banked_amount'),
            ]);

        return response()->json(['data' => $groepen]);
    }

    public function bankVerdachte(BankVerdachteRequest $request, ManageVerdachteBank $manageVerdachteBank): JsonResponse
    {
        $this->ensureDocent();

        $validated = $request->validated();

        $result = $manageVerdachteBank->bankeer(
            (int) $validated['groep_id'],
            (int) $validated['verdachte_nummer']
        );

        return response()->json([
            'message' => $result['message'],
            'data' => [
                'changed' => $result['changed'],
                'amount' => $result['amount'],
            ],
        ]);
    }

    public function confisqueerVerdachte(ConfiscateVerdachteRequest $request, ManageVerdachteBank $manageVerdachteBank): JsonResponse
    {
        $this->ensureDocent();

        $validated = $request->validated();

        $result = $manageVerdachteBank->confisqueer(
            (int) $validated['groep_id'],
            (int) $validated['verdachte_nummer']
        );

        return response()->json([
            'message' => $result['message'],
            'data' => [
                'changed' => $result['changed'],
                'amount' => $result['amount'],
            ],
        ]);
    }

    public function hintOptions(): JsonResponse
    {
        $this->ensureDocent();

        $hints = Hint::query()
            ->orderBy('hint_nummer')
            ->get()
            ->map(fn (Hint $hint) => [
                'id' => $hint->hint_nummer,
                'hint_nummer' => $hint->hint_nummer,
                'beschrijving' => $hint->hint_beschrijving,
            ]);

        $bigBossHints = BigBossHint::query()
            ->orderBy('nummer')
            ->get()
            ->map(fn (BigBossHint $hint) => [
                'id' => $hint->id,
                'nummer' => $hint->nummer,
                'beschrijving' => $hint->beschrijving,
            ]);

        return response()->json([
            'data' => [
                'hints' => $hints,
                'big_boss_hints' => $bigBossHints,
            ],
        ]);
    }

    private function ensureDocent(): void
    {
        if (! Auth::user()?->is_docent) {
            abort(403);
        }
    }
}
