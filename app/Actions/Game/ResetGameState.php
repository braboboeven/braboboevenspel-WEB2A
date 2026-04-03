<?php

namespace App\Actions\Game;

use App\Models\Groep;
use App\Models\GroepScore;
use App\Models\GroepVerdachteBank;
use App\Models\HintVerzending;
use App\Models\Poging;
use Illuminate\Support\Facades\DB;

class ResetGameState
{
    public function __invoke(): void
    {
        DB::transaction(function (): void {
            HintVerzending::query()->delete();
            Poging::query()->delete();
            GroepVerdachteBank::query()->delete();
            GroepScore::query()->delete();
            Groep::query()->delete();
        });
    }
}
