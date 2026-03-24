<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\HintVerzending;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HintController extends Controller
{
    public function index(): JsonResponse
    {
        $groep = Auth::user()?->groepen()->first();
        if (! $groep) {
            return response()->json(['data' => []]);
        }

        $hints = HintVerzending::query()
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
            ]);

        return response()->json(['data' => $hints]);
    }
}
