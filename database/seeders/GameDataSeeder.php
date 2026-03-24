<?php

namespace Database\Seeders;

use App\Actions\Game\OpdrachtParser;
use App\Models\Opdracht;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class GameDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedBoevenData();
        $this->seedOpdrachten();
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

        $parser = new OpdrachtParser();

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
}
