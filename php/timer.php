<?php
include_once 'Itimer.php';

class GameTimer implements Itimer {
    public function startTimer(): bool {
        if (!isset($_SESSION['start_time'])) {
            $_SESSION['start_time'] = time();
            return true;
        }
        return false;
    }

    public function getTimer(): DateTime {
        $startTime = isset($_SESSION['start_time']) ? $_SESSION['start_time'] : time();
        $dt = new DateTime();
        $dt->setTimestamp($startTime);
        return $dt;
    }

    public function getElapsedSeconds(): int {
        if (!isset($_SESSION['start_time'])) return 0;
        return time() - $_SESSION['start_time'];
    }
}
?>