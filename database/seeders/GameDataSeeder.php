<?php

namespace Database\Seeders;

use App\Actions\Game\OpdrachtParser;
use App\Models\Opdracht;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedBoevenData();
        $this->seedOpdrachten();
        $this->seedBigBossOpdrachten();
    }

    private function seedBoevenData(): void
    {
        $verdachteCount = DB::table('Verdachte')->count();
        $misdaadCount = DB::table('Misdaad')->count();

        if ($verdachteCount > 0 || $misdaadCount > 0) {
            return;
        }

        $path = base_path('database/seeders/data/boeven_inserts.sql');
        $contents = file_get_contents($path);

        if ($contents === false) {
            return;
        }

        DB::unprepared($contents);
    }

    private function seedOpdrachten(): void
    {
        if (Opdracht::query()->exists()) {
            return;
        }

        $parser = new OpdrachtParser;

        $verdachtePath = base_path('database/seeders/data/verdachte_opdrachten.sql');
        $misdaadPath = base_path('database/seeders/data/misdaad_opdrachten.sql');

        $verdachteOpdrachten = $parser->parseFromFile($verdachtePath, 'Verdachte', 'V');
        $misdaadOpdrachten = $parser->parseFromFile($misdaadPath, 'Misdaad', 'M');

        $opdrachten = array_merge($verdachteOpdrachten, $misdaadOpdrachten);

        if ($opdrachten === []) {
            return;
        }

        Opdracht::query()->upsert(
            $opdrachten,
            ['code'],
            ['titel', 'prompt', 'correct_query', 'source_table', 'verdachte_nummer', 'step_nummer', 'is_big_boss', 'reward_correct', 'reward_bad_format']
        );
    }

    private function seedBigBossOpdrachten(): void
    {
        $path = base_path('database/seeders/data/big_boss_opdrachten.sql');
        if (! file_exists($path)) {
            return;
        }

        $parser = new OpdrachtParser;
        $opdrachten = $parser->parseFromFile($path, 'Misdaad', 'B');

        if ($opdrachten === []) {
            return;
        }

        $opdrachten = array_map(function (array $opdracht): array {
            $opdracht['is_big_boss'] = true;
            $opdracht['reward_correct'] = 10000;
            $opdracht['reward_bad_format'] = 500;

            return $opdracht;
        }, $opdrachten);

        Opdracht::query()->upsert(
            $opdrachten,
            ['code'],
            ['titel', 'prompt', 'correct_query', 'source_table', 'verdachte_nummer', 'step_nummer', 'is_big_boss', 'reward_correct', 'reward_bad_format']
        );
    }
}
