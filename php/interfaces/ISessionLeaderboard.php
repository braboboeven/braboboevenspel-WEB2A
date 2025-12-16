<?php
interface ISessionLeaderboard
{
    /**
     * Geeft de volledige ranglijst terug, gesorteerd op score (hoog naar laag).
     * @return array<int, array{player: string, score: int}>
     */
    public function leaderboard(string $sessionCode): array;

    /**
     * Voegt een speler toe of werkt de score bij en retourneert het bijgewerkte record.
     * @return array{player: string, score: int}
     */
    public function upsertEntry(string $sessionCode, string $playerName, int $score): array;

    /**
     * Geeft de top N scores voor een sessie terug.
     * @return array<int, array{player: string, score: int}>
     */
    public function top(string $sessionCode, int $limit = 10): array;
}
?>
