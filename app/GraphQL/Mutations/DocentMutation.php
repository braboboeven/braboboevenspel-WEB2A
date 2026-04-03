<?php

namespace App\GraphQL\Mutations;

use App\Actions\Game\FinalizeGameSession;
use App\Actions\Game\ManageVerdachteBank;
use App\Models\BigBossHint;
use App\Models\Hint;
use App\Models\HintVerzending;
use App\Models\SpelSessie;
use Illuminate\Support\Facades\Auth;

class DocentMutation
{
    public function startSpel(): SpelSessie
    {
        $this->ensureDocent();

        return SpelSessie::query()->create([
            'status' => 'running',
            'started_at' => now(),
            'created_by_user_id' => Auth::id(),
        ]);
    }

    public function stopSpel(FinalizeGameSession $finalizeGameSession): SpelSessie
    {
        $this->ensureDocent();

        $sessie = SpelSessie::query()->latest()->first();
        if (! $sessie) {
            abort(404, 'Geen sessie gevonden.');
        }

        $finalizeGameSession($sessie, true);

        return $sessie;
    }

    public function sendHint(?object $root, array $args): bool
    {
        $this->ensureDocent();

        $type = $args['type'];
        $hintId = $args['hint_id'];
        $broadcast = (bool) ($args['broadcast'] ?? true);
        $groepIds = $args['groep_ids'] ?? [];

        if ($type === 'normal' && ! Hint::query()->where('hint_nummer', $hintId)->exists()) {
            abort(404, 'Hint niet gevonden.');
        }

        if ($type === 'bigboss' && ! BigBossHint::query()->where('id', $hintId)->exists()) {
            abort(404, 'Big Boss hint niet gevonden.');
        }

        if (! $broadcast && $groepIds === []) {
            abort(422, 'Selecteer minstens een groep of kies broadcast.');
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

        return true;
    }

    /**
     * @return array{changed: bool, amount: int, message: string}
     */
    public function bankVerdachte(?object $root, array $args, ManageVerdachteBank $manageVerdachteBank): array
    {
        $this->ensureDocent();

        return $manageVerdachteBank->bankeer((int) $args['groep_id'], (int) $args['verdachte_nummer']);
    }

    /**
     * @return array{changed: bool, amount: int, message: string}
     */
    public function confisqueerVerdachte(?object $root, array $args, ManageVerdachteBank $manageVerdachteBank): array
    {
        $this->ensureDocent();

        return $manageVerdachteBank->confisqueer((int) $args['groep_id'], (int) $args['verdachte_nummer']);
    }

    private function ensureDocent(): void
    {
        if (! Auth::user()?->is_docent) {
            abort(403);
        }
    }
}
