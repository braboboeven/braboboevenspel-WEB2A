<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/ScoreRegistrar.php';
require_once __DIR__ . '/SessionLeaderboard.php';
require_once __DIR__ . '/ScoreStorage.php';

class SqlGameEngine
{
    private mysqli $conn;
    private ScoreRegistrar $registrar;
    private SessionLeaderboard $leaderboardService;

    public function __construct(?mysqli $conn = null, ?ScoreRegistrar $registrar = null, ?SessionLeaderboard $leaderboardService = null)
    {
        $this->conn = $conn ?? Database::getConnection();
        $storage = new ScoreStorage($this->conn);
        $this->registrar = $registrar ?? new ScoreRegistrar($storage);
        $this->leaderboardService = $leaderboardService ?? new SessionLeaderboard($storage);
    }

    public function listCases(): array
    {
        $sql = "SELECT c.id, c.case_code, c.title, c.approach, c.total_queries, c.reward_full, c.reward_partial FROM CaseFile c ORDER BY c.id ASC";
        $result = $this->conn->query($sql);
        $cases = [];
        while ($row = $result->fetch_assoc()) {
            $cases[] = $row;
        }
        return $cases;
    }

    public function nextHint(string $caseCode, string $sessionCode, string $groupName): ?array
    {
        $sql = "SELECT h.id, h.seq, h.tekst, h.is_bonus
                FROM Hint h
                JOIN CaseFile c ON h.case_id = c.id
                WHERE c.case_code = ?
                  AND NOT EXISTS (
                        SELECT 1 FROM Submission s
                        WHERE s.hint_id = h.id
                          AND s.session_code = ?
                          AND s.group_name = ?
                )
                ORDER BY h.seq ASC
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sss', $caseCode, $sessionCode, $groupName);
        $stmt->execute();
        $stmt->bind_result($id, $seq, $tekst, $isBonus);
        if ($stmt->fetch()) {
            $stmt->close();
            return [
                'hint_id' => (int)$id,
                'sequence' => (int)$seq,
                'tekst' => $tekst,
                'is_bonus' => (bool)$isBonus
            ];
        }
        $stmt->close();
        return null;
    }

    public function submitSolution(string $sessionCode, string $groupName, int $hintId, string $submittedSql): array
    {
        $hint = $this->getHint($hintId);
        if (!$hint) {
            return ['status' => 'error', 'message' => 'Onbekende hint'];
        }

        $case = $this->getCaseById($hint['case_id']);
        if (!$case) {
            return ['status' => 'error', 'message' => 'Onbekende zaak'];
        }

        $expectedSql = $hint['expected_sql'];
        $canonicalExpected = $this->canonicalize($expectedSql);
        $canonicalSubmitted = $this->canonicalize($submittedSql);

        $isCorrect = $canonicalSubmitted === $canonicalExpected;
        $fullReward = $hint['reward_full'] ?? $case['reward_full'];
        $partialReward = $hint['reward_partial'] ?? $case['reward_partial'];

        $awardedPoints = 0;
        if ($isCorrect) {
            $awardedPoints = $this->isNeatlyFormatted($submittedSql, $expectedSql) ? $fullReward : $partialReward;
        }

        $existing = $this->getSubmission($sessionCode, $groupName, $hintId);
        $previousPoints = $existing ? (int)$existing['awarded_points'] : 0;
        $delta = max(0, $awardedPoints - $previousPoints);

        // upsert submission
        $this->saveSubmission(
            $sessionCode,
            $groupName,
            $case['id'],
            $hintId,
            $submittedSql,
            $awardedPoints,
            $isCorrect
        );

        if ($delta > 0) {
            $this->registrar->registerAnswer($sessionCode, $groupName, $hintId, true, $delta);
        }

        $totalScore = $this->registrar->getScore($sessionCode, $groupName);

        return [
            'status' => 'ok',
            'hint_id' => $hintId,
            'case_code' => $case['case_code'],
            'is_correct' => $isCorrect,
            'awarded_points' => $awardedPoints,
            'delta_points' => $delta,
            'total_score' => $totalScore,
            'reward_full' => $fullReward,
            'reward_partial' => $partialReward,
        ];
    }

    public function leaderboard(string $sessionCode, int $limit = 10): array
    {
        return $this->leaderboardService->top($sessionCode, $limit);
    }

    private function getHint(int $hintId): ?array
    {
        $stmt = $this->conn->prepare("SELECT id, case_id, seq, tekst, expected_sql, is_bonus, reward_full, reward_partial FROM Hint WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $hintId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    private function getCaseById(int $caseId): ?array
    {
        $stmt = $this->conn->prepare("SELECT id, case_code, title, suspect_id, approach, reward_full, reward_partial FROM CaseFile WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $caseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    private function getSubmission(string $sessionCode, string $groupName, int $hintId): ?array
    {
        $stmt = $this->conn->prepare("SELECT id, awarded_points FROM Submission WHERE session_code = ? AND group_name = ? AND hint_id = ? LIMIT 1");
        $stmt->bind_param('ssi', $sessionCode, $groupName, $hintId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    private function saveSubmission(string $sessionCode, string $groupName, int $caseId, int $hintId, string $submittedSql, int $awardedPoints, bool $isCorrect): void
    {
        $stmt = $this->conn->prepare("INSERT INTO Submission (session_code, group_name, case_id, hint_id, submitted_sql, awarded_points, is_correct)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE submitted_sql = VALUES(submitted_sql), awarded_points = VALUES(awarded_points), is_correct = VALUES(is_correct), created_at = created_at");
        $stmt->bind_param('ssiisii', $sessionCode, $groupName, $caseId, $hintId, $submittedSql, $awardedPoints, $isCorrect);
        $stmt->execute();
        $stmt->close();
    }

    private function canonicalize(string $sql): string
    {
        $sql = trim($sql);
        $sql = rtrim($sql, ";\t\r\n ");
        $sql = preg_replace('/\s+/', ' ', $sql);
        return strtoupper($sql);
    }

    private function isNeatlyFormatted(string $submitted, string $expected): bool
    {
        $cleanSubmitted = $this->normalizeWhitespace($submitted);
        $cleanExpected = $this->normalizeWhitespace($expected);
        return $cleanSubmitted === $cleanExpected;
    }

    private function normalizeWhitespace(string $sql): string
    {
        $sql = trim($sql);
        // bewaar line breaks door eerst tabs/spaces te normaliseren
        $sql = preg_replace('/[ \t]+/', ' ', $sql);
        $sql = preg_replace('/\s+/', ' ', $sql);
        return rtrim($sql, ';');
    }
}

?>
