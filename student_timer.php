<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check of student is ingelogd
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header('Location: index.php');
    exit;
}

// GEBRUIK db_connect.php
require_once 'db_connect.php';

$gebruiker = $_SESSION['naam'];
$klas = $_SESSION['klas'];

// Functie voor tijd formatteren
function formatTime($seconds) {
    $seconds = (int)$seconds;
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $h, $m, $s);
}

// Database setup (als tabel nog niet bestaat)
try {
    $conn->query("CREATE TABLE IF NOT EXISTS timers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gebruiker VARCHAR(100) NOT NULL,
        start DATETIME NOT NULL,
        stop DATETIME DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_stop (stop),
        INDEX idx_gebruiker (gebruiker)
    )");
} catch (Exception $e) {
    // Negeer als tabellen al bestaan
}

// Acties afhandelen
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($_POST['action'] === 'start') {
            $stmt = $conn->prepare("SELECT id FROM timers WHERE gebruiker = ? AND stop IS NULL");
            $stmt->bind_param("s", $gebruiker);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->fetch_assoc()) {
                $error = "Je hebt al een actieve timer!";
            } else {
                $stmt = $conn->prepare("INSERT INTO timers (gebruiker, start) VALUES (?, NOW())");
                $stmt->bind_param("s", $gebruiker);
                $stmt->execute();
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }
        
        if ($_POST['action'] === 'stop' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE timers SET stop = NOW() WHERE id = ? AND gebruiker = ? AND stop IS NULL");
            $stmt->bind_param("is", $id, $gebruiker);
            $stmt->execute();
            
            // Update Score (aantal pogingen) en Tijd (totale seconden)
            $update_stmt = $conn->prepare("
                SELECT 
                    COUNT(*) as score,
                    SUM(TIMESTAMPDIFF(SECOND, start, stop)) as total_seconds
                FROM timers 
                WHERE gebruiker = ? AND stop IS NOT NULL
            ");
            $update_stmt->bind_param("s", $gebruiker);
            $update_stmt->execute();
            $result = $update_stmt->get_result();
            $row = $result->fetch_assoc();
            $score = (int)($row['score'] ?? 0);
            $total_tijd = (int)($row['total_seconds'] ?? 0);
            
            // Update Score en Tijd in database
            $save_stmt = $conn->prepare("UPDATE Gebruiker SET Score = ?, Tijd = ? WHERE naam = ?");
            $save_stmt->bind_param("iis", $score, $total_tijd, $gebruiker);
            $save_stmt->execute();
            
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } catch (Exception $e) {
        $error = "Er is een fout opgetreden: " . $e->getMessage();
    }
}

// Haal actieve timer op
$stmt = $conn->prepare("SELECT * FROM timers WHERE gebruiker = ? AND stop IS NULL");
$stmt->bind_param("s", $gebruiker);
$stmt->execute();
$active = $stmt->get_result()->fetch_assoc();

// Haal recente timers op
$stmt = $conn->prepare("SELECT * FROM timers WHERE gebruiker = ? ORDER BY id DESC LIMIT 10");
$stmt->bind_param("s", $gebruiker);
$stmt->execute();
$timers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Statistieken voor vandaag
$today = date('Y-m-d');
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as count,
        SUM(TIMESTAMPDIFF(SECOND, start, stop)) as total_seconds
    FROM timers 
    WHERE gebruiker = ? AND DATE(start) = ? AND stop IS NOT NULL
");
$stmt->bind_param("ss", $gebruiker, $today);
$stmt->execute();
$dagStats = $stmt->get_result()->fetch_assoc();

$totaalVandaag = $dagStats['total_seconds'] ?? 0;
$aantalVandaag = $dagStats['count'] ?? 0;

// Haal leaderboard op (alle gebruikers)
$leaderboard = $conn->query("
    SELECT 
        naam as gebruiker,
        Score as Geld,
        Tijd
    FROM Gebruiker 
    ORDER BY Score DESC, Tijd DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timer - <?= htmlspecialchars($gebruiker) ?></title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            margin: 0; 
            padding: 20px; 
            background: #f8fafc;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .user-info h1 {
            margin: 0;
            color: #1e293b;
            font-size: 24px;
        }
        
        .user-info p {
            margin: 5px 0 0 0;
            color: #64748b;
            font-size: 14px;
        }
        
        .logout-btn {
            padding: 10px 20px;
            background: #ef4444;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .logout-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }
        
        .timer-box { 
            position: fixed; 
            top: 20px; 
            right: 20px; 
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white; 
            padding: 20px 25px; 
            border-radius: 12px; 
            box-shadow: 0 8px 24px rgba(220, 38, 38, 0.3);
            min-width: 200px;
            z-index: 1000;
        }
        
        .tijd { 
            font-size: 42px; 
            font-weight: bold; 
            text-align: center; 
            margin-bottom: 15px;
            font-variant-numeric: tabular-nums;
            letter-spacing: 2px;
        }
        
        button { 
            width: 100%; 
            padding: 10px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: 600; 
            font-size: 15px;
            transition: all 0.2s ease;
        }
        
        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .start { 
            background: white; 
            color: #dc2626; 
        }
        
        .start:hover {
            background: #f5f5f5;
        }
        
        .stop { 
            background: rgba(0,0,0,0.2); 
            color: white; 
        }
        
        .stop:hover {
            background: rgba(0,0,0,0.3);
        }
        
        .stats-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-label {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #1e293b;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        
        h2 {
            margin: 0 0 20px 0;
            color: #1e293b;
            font-size: 20px;
        }
        
        table { 
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td { 
            padding: 12px 16px; 
            text-align: left; 
            border-bottom: 1px solid #e2e8f0; 
        }
        
        th { 
            background: #f8fafc; 
            font-weight: 600;
            color: #475569;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            color: #1e293b;
            font-size: 14px;
        }
        
        tr:hover {
            background: #f8fafc;
        }
        
        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }
        
        .rank {
            font-weight: bold;
            color: #64748b;
        }
        
        .rank-1 { color: #f59e0b; }
        .rank-2 { color: #94a3b8; }
        .rank-3 { color: #cd7f32; }
        
        .current-user {
            background: #dbeafe !important;
        }
        
        @media (max-width: 968px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .timer-box {
                position: static;
                margin-bottom: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .stats-box {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="user-info">
                <h1>👋 Welkom, <?= htmlspecialchars($gebruiker) ?></h1>
                <p>Klas: <?= htmlspecialchars($klas) ?></p>
            </div>
            <a href="index.php?logout=1" class="logout-btn">Uitloggen</a>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="timer-box">
            <div class="tijd" id="timer">00:00:00</div>
            <?php if ($active): ?>
                <form method="POST">
                    <input type="hidden" name="action" value="stop">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($active['id']) ?>">
                    <button class="stop" type="submit">⏹ Stop Timer</button>
                </form>
            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="action" value="start">
                    <button class="start" type="submit">▶ Start Timer</button>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="stats-box">
            <div class="stat-item">
                <div class="stat-label">Vandaag Totaal</div>
                <div class="stat-value">
                    <?php 
                        $h = floor($totaalVandaag / 3600);
                        $m = floor(($totaalVandaag % 3600) / 60);
                        echo sprintf('%dh %dm', $h, $m);
                    ?>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-label">tijd</div>
                <div class="stat-value"><?= $aantalVandaag ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Gemiddeld</div>
                <div class="stat-value">
                    <?php 
                        if ($aantalVandaag > 0) {
                            $gem = $totaalVandaag / $aantalVandaag;
                            echo sprintf('%dm', round($gem / 60));
                        } else {
                            echo '0m';
                        }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="content-grid">
            <div class="table-container">
                <h2>🏆 Leaderboard</h2>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Naam</th>
                            <th>Score (Geld)</th>
                            <th>Tijd</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($leaderboard as $entry): 
                            $isCurrentUser = $entry['gebruiker'] === $gebruiker;
                        ?>
                        <tr class="<?= $isCurrentUser ? 'current-user' : '' ?>">
                            <td><span class="rank rank-<?= $rank ?>"><?= $rank ?></span></td>
                            <td><?= htmlspecialchars($entry['gebruiker']) ?></td>
                            <td>€ <?= $entry['Geld'] ?></td>
                            <td><?= formatTime($entry['Tijd']) ?></td>
                        </tr>
                        <?php 
                        $rank++;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div class="table-container">
                <h2>⏱️ Mijn Recente Timers</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Start</th>
                            <th>Stop</th>
                            <th>Duur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($timers as $t): 
                            $duur = $t['stop'] ? (strtotime($t['stop']) - strtotime($t['start'])) : 0;
                        ?>
                        <tr>
                            <td><?= date('d-m', strtotime($t['start'])) ?></td>
                            <td><?= date('H:i', strtotime($t['start'])) ?></td>
                            <td><?= $t['stop'] ? date('H:i', strtotime($t['stop'])) : '-' ?></td>
                            <td><?= formatTime($duur) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        <?php if ($active): ?>
        function updateTimer() {
            const start = new Date('<?= $active['start'] ?>').getTime();
            const diff = Math.floor((Date.now() - start) / 1000);
            const h = Math.floor(diff / 3600);
            const m = Math.floor((diff % 3600) / 60);
            const s = diff % 60;
            document.getElementById('timer').textContent = 
                String(h).padStart(2, '0') + ':' + 
                String(m).padStart(2, '0') + ':' + 
                String(s).padStart(2, '0');
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);
        <?php endif; ?>
    </script>
</body>
</html>