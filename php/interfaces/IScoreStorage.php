<?php
interface IScoreStorage
{
    /**
     * Slaat de score van een speler in een sessie op.
     */
    public function save(string $sessionCode, string $playerName, int $score): bool;

    /**
     * Haalt de score van een speler op. Retourneert 0 wanneer nog geen score bestaat.
     */
    public function get(string $sessionCode, string $playerName): int;

    /**
     * Geeft alle scores voor een sessie terug als associatieve array: speler => score.
     */
    public function all(string $sessionCode): array;

    /**
     * Verwijdert alle scores die bij een sessie horen.
     */
    public function clearSession(string $sessionCode): bool;
}
?>
