<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\CreateGroepRequest;
use App\Http\Requests\JoinGroepRequest;
use App\Http\Controllers\Controller;
use App\Models\Groep;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GroepController extends Controller
{
    public function me(): JsonResponse
    {
        $groep = Auth::user()?->groepen()->first();

        if (! $groep) {
            return response()->json(['data' => null]);
        }

        return response()->json([
            'data' => [
                'id' => $groep->id,
                'naam' => $groep->naam,
                'klas' => $groep->klas,
                'code' => $groep->code,
            ],
        ]);
    }

    public function store(CreateGroepRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $groep = Groep::create([
            'naam' => $validated['naam'],
            'klas' => $validated['klas'] ?? null,
            'code' => Str::upper(Str::random(8)),
        ]);

        $groep->gebruikers()->attach(Auth::id(), ['is_leider' => true]);

        return response()->json([
            'data' => [
                'id' => $groep->id,
                'naam' => $groep->naam,
                'klas' => $groep->klas,
                'code' => $groep->code,
            ],
        ], 201);
    }

    public function join(JoinGroepRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $groep = Groep::query()->where('code', $validated['code'])->first();

        if (! $groep) {
            return response()->json(['message' => 'Groepcode niet gevonden.'], 404);
        }

        $groep->gebruikers()->syncWithoutDetaching([Auth::id() => ['is_leider' => false]]);

        return response()->json([
            'data' => [
                'id' => $groep->id,
                'naam' => $groep->naam,
                'klas' => $groep->klas,
                'code' => $groep->code,
            ],
        ]);
    }
}
