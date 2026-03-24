<?php

namespace App\Actions\Game;

use Illuminate\Support\Str;

class OpdrachtParser
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function parseFromFile(string $path, string $sourceTable, string $prefix): array
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        return $this->parseFromString($contents, $sourceTable, $prefix);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function parseFromString(string $contents, string $sourceTable, string $prefix): array
    {
        $lines = preg_split('/\R/', $contents) ?: [];

        $opdrachten = [];
        $currentStep = null;
        $currentPrompt = [];
        $currentQuery = [];
        $inQuery = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (preg_match('/^--\s+(\d+)\.(\d+)/', $trimmed, $matches)) {
                $currentStep = $matches[1].'.'.$matches[2];
                $currentPrompt = [];
                $currentQuery = [];
                $inQuery = false;
                continue;
            }

            if (! $currentStep) {
                continue;
            }

            if (Str::startsWith($trimmed, '--')) {
                if (
                    Str::contains($trimmed, ['VOORKANT', 'ACHTERKANT', 'Juiste antwoord', 'info voor docent'])
                    || preg_match('/\brows\b/i', $trimmed)
                ) {
                    continue;
                }

                $promptLine = trim(Str::after($trimmed, '--'));
                if ($promptLine !== '') {
                    $currentPrompt[] = $promptLine;
                }

                continue;
            }

            if (Str::startsWith(Str::lower($trimmed), 'select')) {
                $inQuery = true;
                $currentQuery[] = $trimmed;
                if (Str::endsWith($trimmed, ';')) {
                    $inQuery = false;
                    $opdrachten[] = $this->buildOpdracht($currentStep, $currentPrompt, $currentQuery, $sourceTable, $prefix);
                    $currentQuery = [];
                }

                continue;
            }

            if ($inQuery) {
                $currentQuery[] = $trimmed;
                if (Str::endsWith($trimmed, ';')) {
                    $inQuery = false;
                    $opdrachten[] = $this->buildOpdracht($currentStep, $currentPrompt, $currentQuery, $sourceTable, $prefix);
                    $currentQuery = [];
                }
            }
        }

        return array_filter($opdrachten);
    }

    /**
     * @param array<int, string> $promptLines
     * @param array<int, string> $queryLines
     * @return array<string, mixed>
     */
    private function buildOpdracht(string $step, array $promptLines, array $queryLines, string $sourceTable, string $prefix): array
    {
        $prompt = trim(implode(' ', $promptLines));
        $query = trim(implode("\n", $queryLines));
        $query = rtrim($query, ';');

        [$verdachteNummer, $stepNummer] = array_pad(explode('.', $step), 2, null);

        return [
            'code' => $prefix.$step,
            'titel' => $prefix.' '.$step,
            'prompt' => $prompt !== '' ? $prompt : 'Opdracht '.$step,
            'correct_query' => $query,
            'source_table' => $sourceTable,
            'verdachte_nummer' => $verdachteNummer ? (int) $verdachteNummer : null,
            'step_nummer' => $stepNummer ? (int) $stepNummer : null,
            'is_big_boss' => false,
            'reward_correct' => 1000,
            'reward_bad_format' => 500,
        ];
    }
}
