<?php
session_start();

// Check of docent is ingelogd
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'docent') {
    header('Location: index.php');
    exit;
}

// GEBRUIK db_connect.php
require_once 'db_connect.php';

$docent = $_SESSION['naam'];

// Functie voor tijd formatteren
function formatTime($seconds) {
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
    // Negeer als tabel al bestaat
}

// Haal alle studenten met statistieken op
$allStudents = $conn->query("
    SELECT 
        gebruiker,
        COUNT(*) as totaal_sessies,
        SUM(TIMESTAMPDIFF(SECOND, start, stop)) as totaal_seconden,
        MAX(start) as laatste_activiteit
    FROM timers 
    WHERE stop IS NOT NULL 
    GROUP BY gebruiker 
    ORDER BY totaal_seconden DESC
")->fetch_all(MYSQLI_ASSOC);

// Statistieken van vandaag
$today = date('Y-m-d');
$todayStats = $conn->query("
    SELECT 
        COUNT(DISTINCT gebruiker) as actieve_studenten,
        COUNT(*) as totaal_sessies,
        SUM(TIMESTAMPDIFF(SECOND, start, stop)) as totaal_seconden
    FROM timers 
    WHERE DATE(start) = '$today' AND stop IS NOT NULL
")->fetch_assoc();

// Langzaamste studenten (minste tijd)
$langzaamste = $conn->query("
    SELECT 
        gebruiker,
        SUM(TIMESTAMPDIFF(SECOND, start, stop)) as totaal_seconden,
        COUNT(*) as sessies
    FROM timers 
    WHERE stop IS NOT NULL 
    GROUP BY gebruiker 
    ORDER BY totaal_seconden ASC 
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

// Snelste studenten (meeste tijd)
$snelste = $conn->query("
    SELECT 
        gebruiker,
        SUM(TIMESTAMPDIFF(SECOND, start, stop)) as totaal_seconden,
        COUNT(*) as sessies
    FROM timers 
    WHERE stop IS NOT NULL 
    GROUP BY gebruiker 
    ORDER BY totaal_seconden DESC 
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

// Recente activiteit
$recenteActiviteit = $conn->query("
    SELECT * FROM timers 
    ORDER BY start DESC 
    LIMIT 20
")->fetch_all(MYSQLI_ASSOC);

// Actieve timers
$activeTimers = $conn->query("
    SELECT * FROM timers 
    WHERE stop IS NULL 
    ORDER BY start DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docenten Dashboard - <?= htmlspecialchars($docent) ?></title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            margin: 0; 
            padding: 20px; 
            background: #f8fafc;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .logout-btn {
            padding: 10px 20px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .logout-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #64748b;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .stat-card .value {
            font-size: 36px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .stat-card .label {
            color: #64748b;
            font-size: 13px;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        .table-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        h2 {
            margin: 0 0 20px 0;
            color: #1e293b;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tab-container {
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 20px;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .tab {
            padding: 10px 20px;
            border: none;
            background: none;
            color: #64748b;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .tab:hover {
            color: #1e293b;
        }
        
        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
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
        
        .rank {
            font-weight: bold;
            font-size: 16px;
        }
        
        .rank-1 { color: #f59e0b; }
        .rank-2 { color: #94a3b8; }
        .rank-3 { color: #cd7f32; }
        
        .warning-row {
            background: #fef3c7 !important;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-actief {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-gestopt {
            background: #f1f5f9;
            color: #475569;
        }
        
        .live-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @media (max-width: 968px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>📊 Docenten Dashboard</h1>
                <p>Welkom, <?= htmlspecialchars($docent) ?></p>
            </div>
            <a href="index.php?logout=1" class="logout-btn">Uitloggen</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Vandaag Actief</h3>
                <div class="value"><?= $todayStats['actieve_studenten'] ?? 0 ?></div>
                <div class="label">studenten</div>
            </div>
            
            <div class="stat-card">
                <h3>Totaal Sessies Vandaag</h3>
                <div class="value"><?= $todayStats['totaal_sessies'] ?? 0 ?></div>
                <div class="label">sessies</div>
            </div>
            
            <div class="stat-card">
                <h3>Totale Tijd Vandaag</h3>
                <div class="value">
                    <?php 
                        $sec = $todayStats['totaal_seconden'] ?? 0;
                        $h = floor($sec / 3600);
                        $m = floor(($sec % 3600) / 60);
                        echo sprintf('%dh %dm', $h, $m);
                    ?>
                </div>
                <div class="label">studietijd</div>
            </div>
            
            <div class="stat-card">
                <h3>Nu Actief</h3>
                <div class="value"><?= count($activeTimers) ?></div>
                <div class="label">
                    <?php if (count($activeTimers) > 0): ?>
                        <span class="live-indicator"></span>live
                    <?php else: ?>
                        niemand
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (count($activeTimers) > 0): ?>
        <div class="table-container full-width">
            <h2>🟢 Actieve Timers (Live)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Gestart om</th>
                        <th>Looptijd</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activeTimers as $timer): 
                        $looptijd = time() - strtotime($timer['start']);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($timer['gebruiker']) ?></td>
                        <td><?= date('H:i', strtotime($timer['start'])) ?></td>
                        <td><?= formatTime($looptijd) ?></td>
                        <td><span class="status-badge status-actief">● Actief</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="table-container full-width">
            <div class="tab-container">
                <div class="tabs">
                    <button class="tab active" onclick="switchTab('alle')">Alle Studenten</button>
                    <button class="tab" onclick="switchTab('top')">Top Performers</button>
                    <button class="tab" onclick="switchTab('aandacht')">Aandacht Nodig</button>
                    <button class="tab" onclick="switchTab('recent')">Recente Activiteit</button>
                </div>
            </div>
            
            <div id="alle" class="tab-content active">
                <h2>👥 Alle Studenten (<?= count($allStudents) ?>)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Totale Tijd</th>
                            <th>Sessies</th>
                            <th>Gem. Sessie</th>
                            <th>Laatste Activiteit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($allStudents as $student): 
                            $totaal_h = floor($student['totaal_seconden'] / 3600);
                            $totaal_m = floor(($student['totaal_seconden'] % 3600) / 60);
                            $gem = $student['totaal_seconden'] / $student['totaal_sessies'];
                            
                            $warning = $student['totaal_seconden'] < 3600 ? 'warning-row' : '';
                        ?>
                        <tr class="<?= $warning ?>">
                            <td><span class="rank rank-<?= $rank ?>"><?= $rank ?></span></td>
                            <td><?= htmlspecialchars($student['gebruiker']) ?></td>
                            <td><?= sprintf('%dh %dm', $totaal_h, $totaal_m) ?></td>
                            <td><?= $student['totaal_sessies'] ?></td>
                            <td><?= sprintf('%dm', round($gem / 60)) ?></td>
                            <td><?= date('d-m H:i', strtotime($student['laatste_activiteit'])) ?></td>
                        </tr>
                        <?php 
                        $rank++;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div id="top" class="tab-content">
                <h2>🏆 Top 10 - Meeste Studietijd</h2>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Totale Tijd</th>
                            <th>Sessies</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($snelste as $student): 
                            $h = floor($student['totaal_seconden'] / 3600);
                            $m = floor(($student['totaal_seconden'] % 3600) / 60);
                        ?>
                        <tr>
                            <td><span class="rank rank-<?= $rank ?>"><?= $rank ?></span></td>
                            <td><?= htmlspecialchars($student['gebruiker']) ?></td>
                            <td><?= sprintf('%dh %dm', $h, $m) ?></td>
                            <td><?= $student['sessies'] ?></td>
                        </tr>
                        <?php 
                        $rank++;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div id="aandacht" class="tab-content">
                <h2>⚠️ Studenten met Minste Tijd</h2>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Totale Tijd</th>
                            <th>Sessies</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($langzaamste as $student): 
                            $h = floor($student['totaal_seconden'] / 3600);
                            $m = floor(($student['totaal_seconden'] % 3600) / 60);
                            $status = $h < 1 ? 'Weinig actief' : 'Aandacht';
                        ?>
                        <tr class="warning-row">
                            <td><?= $rank ?></td>
                            <td><?= htmlspecialchars($student['gebruiker']) ?></td>
                            <td><?= sprintf('%dh %dm', $h, $m) ?></td>
                            <td><?= $student['sessies'] ?></td>
                            <td><?= $status ?></td>
                        </tr>
                        <?php 
                        $rank++;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div id="recent" class="tab-content">
                <h2>📅 Recente Activiteit</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Start</th>
                            <th>Stop</th>
                            <th>Duur</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recenteActiviteit as $act): 
                            $duur = $act['stop'] ? (strtotime($act['stop']) - strtotime($act['start'])) : (time() - strtotime($act['start']));
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($act['gebruiker']) ?></td>
                            <td><?= date('d-m H:i', strtotime($act['start'])) ?></td>
                            <td><?= $act['stop'] ? date('H:i', strtotime($act['stop'])) : '-' ?></td>
                            <td><?= formatTime($duur) ?></td>
                            <td>
                                <span class="status-badge <?= $act['stop'] ? 'status-gestopt' : 'status-actief' ?>">
                                    <?= $act['stop'] ? 'Gestopt' : '● Actief' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Verberg alle tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Deactiveer alle tab buttons
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Toon geselecteerde tab
            document.getElementById(tabName).classList.add('active');
            
            // Activeer juiste button
            event.target.classList.add('active');
        }
        
        // Auto-refresh elke 30 seconden
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>