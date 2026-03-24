<?php

namespace App\GraphQL\Mutations;

use App\Models\Groep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GroepMutation
{
    public function create(?object $root, array $args): Groep
    {
        $this->ensureAuth();

        $groep = Groep::create([
            'naam' => $args['naam'],
            'klas' => $args['klas'] ?? null,
            'code' => Str::upper(Str::random(8)),
        ]);

        $groep->gebruikers()->attach(Auth::id(), ['is_leider' => true]);

        return $groep;
    }

    public function join(?object $root, array $args): Groep
    {
        $this->ensureAuth();

        $groep = Groep::query()->where('code', $args['code'])->firstOrFail();
        $groep->gebruikers()->syncWithoutDetaching([Auth::id() => ['is_leider' => false]]);

        return $groep;
    }

    private function ensureAuth(): void
    {
        if (! Auth::check()) {
            abort(401);
        }
    }
}
