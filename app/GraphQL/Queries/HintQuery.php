<?php

namespace App\GraphQL\Queries;

use App\Models\HintVerzending;
use Illuminate\Support\Facades\Auth;

class HintQuery
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function forGroup(): array
    {
        $groep = Auth::user()?->groepen()->first();
        if (! $groep) {
            return [];
        }

        return HintVerzending::query()
            ->with(['hint', 'bigBossHint'])
            ->where(function ($query) use ($groep) {
                $query->whereNull('groep_id')
                    ->orWhere('groep_id', $groep->id);
            })
            ->latest('sent_at')
            ->get()
            ->map(fn (HintVerzending $hint) => [
                'id' => $hint->id,
                'type' => $hint->bigBossHint ? 'bigboss' : 'normal',
                'hint_nummer' => $hint->hint?->hint_nummer,
                'beschrijving' => $hint->hint?->hint_beschrijving ?? $hint->bigBossHint?->beschrijving,
                'sent_at' => $hint->sent_at?->toDateTimeString(),
            ])
            ->all();
    }
}
