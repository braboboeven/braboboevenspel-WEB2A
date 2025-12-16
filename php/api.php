<?php

header('Content-Type: application/json');

require_once __DIR__ . '/services/SqlGameEngine.php';
require_once __DIR__ . '/services/SessionLeaderboard.php';

$action = $_GET['action'] ?? $_POST['action'] ?? null;
$engine = new SqlGameEngine();

try {
    switch ($action) {
        case 'cases':
            echo json_encode(['status' => 'ok', 'cases' => $engine->listCases()]);
            break;
        case 'next_hint':
            $caseCode = $_GET['case_code'] ?? $_POST['case_code'] ?? '';
            $sessionCode = $_GET['session_code'] ?? $_POST['session_code'] ?? '';
            $groupName = $_GET['group_name'] ?? $_POST['group_name'] ?? '';
            if (!$caseCode || !$sessionCode || !$groupName) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'case_code, session_code en group_name zijn verplicht']);
                break;
            }
            $hint = $engine->nextHint($caseCode, $sessionCode, $groupName);
            echo json_encode(['status' => $hint ? 'ok' : 'done', 'hint' => $hint]);
            break;
        case 'submit_sql':
            $hintId = (int)($_POST['hint_id'] ?? $_GET['hint_id'] ?? 0);
            $sessionCode = $_POST['session_code'] ?? $_GET['session_code'] ?? '';
            $groupName = $_POST['group_name'] ?? $_GET['group_name'] ?? '';
            $sql = $_POST['sql'] ?? $_GET['sql'] ?? '';
            if (!$hintId || !$sessionCode || !$groupName || !$sql) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'hint_id, session_code, group_name en sql zijn verplicht']);
                break;
            }
            $result = $engine->submitSolution($sessionCode, $groupName, $hintId, $sql);
            echo json_encode($result);
            break;
        case 'leaderboard':
            $sessionCode = $_GET['session_code'] ?? $_POST['session_code'] ?? '';
            $limit = (int)($_GET['limit'] ?? $_POST['limit'] ?? 10);
            if (!$sessionCode) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'session_code is verplicht']);
                break;
            }
            $board = $engine->leaderboard($sessionCode, $limit ?: 10);
            echo json_encode(['status' => 'ok', 'leaderboard' => $board]);
            break;
        default:
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Gebruik action=cases|next_hint|submit_sql|leaderboard'
            ]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
