<?php

namespace App\Actions\Game;

use App\Models\Groep;
use App\Models\GroepScore;
use App\Models\GroepVerdachteBank;
use App\Models\Poging;
use Illuminate\Support\Facades\DB;

class ManageVerdachteBank
{
    /**
     * @return array{changed: bool, amount: int, message: string}
     */
    public function bankeer(int $groepId, int $verdachteNummer): array
    {
        Groep::query()->findOrFail($groepId);

        $earned = $this->earnedForVerdachte($groepId, $verdachteNummer);

        return DB::transaction(function () use ($groepId, $verdachteNummer, $earned): array {
            $bank = GroepVerdachteBank::query()->firstOrCreate(
                [
                    'groep_id' => $groepId,
                    'verdachte_nummer' => $verdachteNummer,
                ],
                [
                    'banked_amount' => 0,
                    'confiscated_amount' => 0,
                ]
            );

            $alreadyProcessed = $bank->banked_amount + $bank->confiscated_amount;
            $available = max(0, $earned - $alreadyProcessed);

            if ($available === 0) {
                return [
                    'changed' => false,
                    'amount' => 0,
                    'message' => 'Geen nieuw bedrag beschikbaar voor deze verdachte.',
                ];
            }

            $bank->banked_amount += $available;
            $bank->last_banked_at = now();
            $bank->save();

            return [
                'changed' => true,
                'amount' => $available,
                'message' => 'Bedrag op bank gezet.',
            ];
        });
    }

    /**
     * @return array{changed: bool, amount: int, message: string}
     */
    public function confisqueer(int $groepId, int $verdachteNummer): array
    {
        Groep::query()->findOrFail($groepId);

        return DB::transaction(function () use ($groepId, $verdachteNummer): array {
            $bank = GroepVerdachteBank::query()
                ->where('groep_id', $groepId)
                ->where('verdachte_nummer', $verdachteNummer)
                ->first();

            if (! $bank || $bank->banked_amount <= 0) {
                return [
                    'changed' => false,
                    'amount' => 0,
                    'message' => 'Er staat geen bedrag op de bank voor deze verdachte.',
                ];
            }

            $amount = $bank->banked_amount;

            $bank->banked_amount = 0;
            $bank->confiscated_amount += $amount;
            $bank->confiscated_at = now();
            $bank->save();

            $score = GroepScore::query()->firstOrCreate(
                ['groep_id' => $groepId],
                ['score' => 0, 'big_boss_score' => 0]
            );

            $score->score = max(0, $score->score - $amount);
            $score->save();

            return [
                'changed' => true,
                'amount' => $amount,
                'message' => 'Bedrag in beslag genomen.',
            ];
        });
    }

    private function earnedForVerdachte(int $groepId, int $verdachteNummer): int
    {
        return (int) Poging::query()
            ->where('groep_id', $groepId)
            ->whereHas('opdracht', function ($query) use ($verdachteNummer) {
                $query->where('is_big_boss', false)
                    ->where('verdachte_nummer', $verdachteNummer);
            })
            ->sum('earned');
    }
}
