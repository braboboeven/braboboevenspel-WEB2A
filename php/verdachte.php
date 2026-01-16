<?php
session_start();

// Interface definitie
interface Itimer
{
    public function getTimer() :DateTime;
    public function startTimer(): bool;
}

// Implementatie van de Timer klasse met Database opslag
class GameTimer implements Itimer {
    private $db;

    public function __construct($dbConnection = null) {
        $this->db = $dbConnection;
    }

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

    // Slaat resultaten op in de tabel leerlingen
    public function saveResult($naam, $score, $tijd) {
        if ($this->db) {
            $stmt = $this->db->prepare("INSERT INTO leerlingen (Naam, score, timer) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $naam, $score, $tijd);
            return $stmt->execute();
        }
        return false;
    }
}

include 'AwnserSystem.php';
$awnserSystem = new AwnserSystem();
// Geef de database verbinding mee aan de timer
$timer = new GameTimer($awnserSystem->GetConnection());

$vragen = $awnserSystem->GetQuestions();
$student_naam = isset($_SESSION['naam']) ? $_SESSION['naam'] : "";
$huidig_vraag_index = isset($_SESSION['vraag_index']) ? $_SESSION['vraag_index'] : 0;
$totaal_score = isset($_SESSION['totaal_score']) ? $_SESSION['totaal_score'] : 0;
$student_query = "";
$resultaat = null;
$huidige_vraag = null;

// Restart logica
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restart'])) {
    unset($_SESSION['naam'], $_SESSION['vraag_index'], $_SESSION['totaal_score'], $_SESSION['start_time'], $_SESSION['resultaat_opgeslagen']);
    session_regenerate_id(true);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Login logica
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['naam']) && !isset($_SESSION['naam'])) {
    $_SESSION['naam'] = trim($_POST['naam']);
    $_SESSION['vraag_index'] = 0;
    $_SESSION['totaal_score'] = 0;
    $timer->startTimer();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Volgende vraag
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['volgende'])) {
    $_SESSION['vraag_index'] = isset($_SESSION['vraag_index']) ? $_SESSION['vraag_index'] + 1 : 1;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Query controle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query'])) {
    $student_query = $_POST['query'];
    if ($huidig_vraag_index < count($vragen)) {
        $huidige_vraag = $vragen[$huidig_vraag_index];
        $resultaat = $awnserSystem->CheckQuery($student_query, $huidige_vraag['correcte_query'], $huidige_vraag['verwacht_resultaat_aantal'], null);
        $_SESSION['totaal_score'] += $resultaat['score'];
        $totaal_score = $_SESSION['totaal_score'];
    }
}

if ($huidig_vraag_index < count($vragen) && !$huidige_vraag) {
    $huidige_vraag = $vragen[$huidig_vraag_index];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>brabo-boevenspel</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/timer.js" defer></script>
</head>
<body>

<?php if (!$student_naam): ?>
    <div class="login-container">
        <h1 class="logo-box">brabo-boevenspel</h1>
        <form method="POST">
            <div class="input-area">
                <input type="text" name="naam" placeholder="naam" required autofocus>
            </div>
            <br>
            <button type="submit" class="btn-start">START SPEL</button>
        </form>
    </div>

<?php elseif ($huidig_vraag_index >= count($vragen)): ?>
    <?php 
        // Bereken eindtijd voor weergave en database
        $verschil = time() - $_SESSION['start_time'];
        $eindtijd = str_pad(floor($verschil / 60), 2, '0', STR_PAD_LEFT) . ":" . str_pad($verschil % 60, 2, '0', STR_PAD_LEFT);

        // Sla resultaat éénmalig op in de database
        if (!isset($_SESSION['resultaat_opgeslagen'])) {
            $timer->saveResult($student_naam, $totaal_score, $eindtijd);
            $_SESSION['resultaat_opgeslagen'] = true;
        }
    ?>
    <div class="header-bar">
        <div class="header-box score-box"><?php echo str_pad($totaal_score, 4, '0', STR_PAD_LEFT); ?></div>
        <div class="header-box timer-box">FINISH</div>
        <div class="header-box vraag-box">END</div>
    </div>

    <div class="game-container">
        <?php if ($totaal_score >= 10000): ?>
            <h1 class='logo-box' style='border-color: #4caf50;'>🏆 GEWONNEN!</h1>
            <p>Geweldig gedaan!</p>
        <?php else: ?>
            <h1 class='logo-box' style='border-color: #e35f5f;'>❌ VERLOREN...</h1>
            <p>Niet genoeg verdiend. Probeer het opnieuw.</p>
        <?php endif; ?>

        <div class="input-area" style="margin: 20px 0;">
            <p>Resultaat opgeslagen voor: <strong><?php echo htmlspecialchars($student_naam); ?></strong></p>
            <p><strong>Tijd:</strong> <?php echo $eindtijd; ?> | <strong>Score:</strong> €<?php echo $totaal_score; ?></p>
        </div>
        <form method="POST"><button type="submit" name="restart" class="btn-check">Opnieuw</button></form>
    </div>

<?php else: ?>
    <div class="header-bar">
        <div class="header-box score-box"><?php echo str_pad($totaal_score, 4, '0', STR_PAD_LEFT); ?></div>
        <div class="header-box timer-box" id="display-timer">00:00</div>
        <div class="header-box vraag-box"><?php echo ($huidig_vraag_index + 1); ?></div>
    </div>
    
    <div class="game-container">
        <div class="vraag-tekst">-- <?php echo $huidige_vraag['vraag_tekst']; ?></div>
        <?php if (!$resultaat): ?>
            <form method="POST">
                <div class="input-area"><textarea name="query" required autofocus></textarea></div>
                <br><button type="submit" class="btn-check">✓ Controleer</button>
            </form>
        <?php else: ?>
            <div class="feedback-container">
                <div class="feedback-box <?php echo $resultaat['score'] == 0 ? 'fout' : ($resultaat['score'] == 1000 ? 'goed' : 'deels'); ?>">
                    <code><?php echo nl2br(htmlspecialchars($student_query)); ?></code>
                </div>
                <p class="status-tekst"><?php echo $resultaat['score'] == 0 ? 'onjuist' : ($resultaat['score'] == 1000 ? 'goed' : 'deels goed'); ?></p>
                <p class="feedback-tekst-klein"><?php echo nl2br($resultaat['feedback']); ?></p>
            </div>
            <form method="POST"><button type="submit" name="volgende" value="1" class="btn-next">Volgende →</button></form>
        <?php endif; ?>
    </div>

    <script>
        window.onload = function() {
            startLiveTimer(<?php echo $_SESSION['start_time']; ?>);
        };
    </script>
<?php endif; ?>

</body>
</html>