<?php

require_once __DIR__ . '/../interfaces/IScoreStorage.php';
require_once __DIR__ . '/Database.php';

class ScoreStorage implements IScoreStorage
{
    private mysqli $conn;

    public function __construct(?mysqli $conn = null)
    {
        $this->conn = $conn ?? Database::getConnection();
    }

    public function save(string $sessionCode, string $playerName, int $score): bool
    {
        $sql = "INSERT INTO SessionScore (session_code, player_name, score) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE score = VALUES(score), updated_at = CURRENT_TIMESTAMP";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ssi', $sessionCode, $playerName, $score);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function get(string $sessionCode, string $playerName): int
    {
        $sql = "SELECT score FROM SessionScore WHERE session_code = ? AND player_name = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return 0;
        }
        $stmt->bind_param('ss', $sessionCode, $playerName);
        $stmt->execute();
        $stmt->bind_result($score);
        $found = $stmt->fetch();
        $stmt->close();
        return $found ? (int)$score : 0;
    }

    public function all(string $sessionCode): array
    {
        $sql = "SELECT player_name, score FROM SessionScore WHERE session_code = ? ORDER BY score DESC, updated_at ASC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('s', $sessionCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[$row['player_name']] = (int)$row['score'];
        }
        $stmt->close();
        return $rows;
    }

    public function clearSession(string $sessionCode): bool
    {
        $sql = "DELETE FROM SessionScore WHERE session_code = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('s', $sessionCode);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}

?>
