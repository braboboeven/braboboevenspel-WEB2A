<?php
interface IScoreRegistrar
{
    /**
     * Registreert het antwoord van een speler en geeft de bijgewerkte totaalscore terug.
     * Gebruik een puntwaarde van 0 voor foute antwoorden.
     */
    public function registerAnswer(string $sessionCode, string $playerName, int $questionId, bool $isCorrect, int $pointsAwarded): int;

    /**
     * Haalt de huidige score van een speler op binnen een sessie.
     */
    public function getScore(string $sessionCode, string $playerName): int;

    /**
     * Reset de score van een specifieke speler binnen een sessie.
     */
    public function resetPlayer(string $sessionCode, string $playerName): bool;

    /**
     * Reset alle spelersscores binnen een sessie.
     */
    public function resetSession(string $sessionCode): bool;
}
?>
