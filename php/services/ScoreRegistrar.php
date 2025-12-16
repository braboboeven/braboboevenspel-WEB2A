<?php

require_once __DIR__ . '/../interfaces/IScoreRegistrar.php';
require_once __DIR__ . '/ScoreStorage.php';

class ScoreRegistrar implements IScoreRegistrar
{
    private ScoreStorage $storage;

    public function __construct(?ScoreStorage $storage = null)
    {
        $this->storage = $storage ?? new ScoreStorage();
    }

    public function registerAnswer(string $sessionCode, string $playerName, int $questionId, bool $isCorrect, int $pointsAwarded): int
    {
        // only award points when correct; clamp to >=0
        $points = $isCorrect ? max(0, $pointsAwarded) : 0;
        $current = $this->storage->get($sessionCode, $playerName);
        $newScore = $current + $points;
        $this->storage->save($sessionCode, $playerName, $newScore);
        return $newScore;
    }

    public function getScore(string $sessionCode, string $playerName): int
    {
        return $this->storage->get($sessionCode, $playerName);
    }

    public function resetPlayer(string $sessionCode, string $playerName): bool
    {
        return $this->storage->save($sessionCode, $playerName, 0);
    }

    public function resetSession(string $sessionCode): bool
    {
        return $this->storage->clearSession($sessionCode);
    }
}

?>
