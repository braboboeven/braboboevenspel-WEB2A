<?php

require_once __DIR__ . '/../interfaces/ISessionLeaderboard.php';
require_once __DIR__ . '/ScoreStorage.php';

class SessionLeaderboard implements ISessionLeaderboard
{
    private ScoreStorage $storage;

    public function __construct(?ScoreStorage $storage = null)
    {
        $this->storage = $storage ?? new ScoreStorage();
    }

    public function leaderboard(string $sessionCode): array
    {
        $scores = $this->storage->all($sessionCode);
        arsort($scores, SORT_NUMERIC);
        $result = [];
        foreach ($scores as $player => $score) {
            $result[] = ['player' => $player, 'score' => $score];
        }
        return $result;
    }

    public function upsertEntry(string $sessionCode, string $playerName, int $score): array
    {
        $this->storage->save($sessionCode, $playerName, $score);
        return ['player' => $playerName, 'score' => $score];
    }

    public function top(string $sessionCode, int $limit = 10): array
    {
        $full = $this->leaderboard($sessionCode);
        return array_slice($full, 0, max(0, $limit));
    }
}

?>
