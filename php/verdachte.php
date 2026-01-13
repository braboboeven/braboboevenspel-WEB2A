<?php
session_start();

// MAAK DE GEBRUIKER AAN DIE ALLEEN BIJ DE VRAGEN KAN EN ALLEEN KAN SELECTEN !!!!
// QUERIES WORDEN UITGEVOERD IN DE LIVE DATABASE !!!!

include 'AwnserSystem.php';
$awnserSystem = new AwnserSystem();

// Verwerk formulier
$vragen = $awnserSystem->GetQuestions();
$student_naam = isset($_SESSION['naam']) ? $_SESSION['naam'] : "";
$huidig_vraag_index = isset($_SESSION['vraag_index']) ? $_SESSION['vraag_index'] : 0;
$totaal_score = isset($_SESSION['totaal_score']) ? $_SESSION['totaal_score'] : 0;
$student_query = "";
$resultaat = null;
$huidige_vraag = null;

// Restart (opnieuw starten)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restart'])) {
    unset($_SESSION['naam']);
    unset($_SESSION['vraag_index']);
    unset($_SESSION['totaal_score']);
    session_regenerate_id(true);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Check of naam is ingevuld (login)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['naam']) && !isset($_SESSION['naam'])) {
    $_SESSION['naam'] = trim($_POST['naam']);
    $_SESSION['vraag_index'] = 0;
    $_SESSION['totaal_score'] = 0;
    $student_naam = $_SESSION['naam'];
    $huidig_vraag_index = 0;
    $totaal_score = 0;
    // Redirect om dubbele POST te voorkomen en sessie direct beschikbaar te maken
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Volgende vraag
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['volgende'])) {
    $_SESSION['vraag_index'] = isset($_SESSION['vraag_index']) ? $_SESSION['vraag_index'] + 1 : 1;
    $huidig_vraag_index = $_SESSION['vraag_index'];
    $resultaat = null;
    // Redirect zodat formulier niet opnieuw wordt verzonden bij refresh
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Check query inzending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query'])) {
    // Zorg dat naam vanuit POST of sessie beschikbaar is
    if (!$student_naam && isset($_POST['naam'])) {
        $_SESSION['naam'] = trim($_POST['naam']);
        $student_naam = $_SESSION['naam'];
    }

    if ($student_naam) {
        $student_query = $_POST['query'];
            if ($huidig_vraag_index < count($vragen)) {
            $huidige_vraag = $vragen[$huidig_vraag_index];
            $student_antwoord = isset($_POST['antwoord']) ? $_POST['antwoord'] : null;
            $resultaat = $awnserSystem->CheckQuery($student_query, $huidige_vraag['correcte_query'], $huidige_vraag['verwacht_resultaat_aantal'], $student_antwoord);
            $_SESSION['totaal_score'] = isset($_SESSION['totaal_score']) ? $_SESSION['totaal_score'] + $resultaat['score'] : $resultaat['score'];
            $totaal_score = $_SESSION['totaal_score'];
        }
    }
}

// Haal huidige vraag
if ($huidig_vraag_index < count($vragen) && !$huidige_vraag) {
    $huidige_vraag = $vragen[$huidig_vraag_index];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Query Checker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if (!$student_naam): ?>
    <!-- LOGIN SCHERM -->
    <div class="login-form">
        <h1>🔍 Query Checker</h1>
        <form method="POST">
            <input type="text" name="naam" placeholder="Vul je naam in..." required>
            <br>
            <button type="submit">Start</button>
        </form>
    </div>

<?php elseif ($huidig_vraag_index >= count($vragen)): ?>
    <!-- GAME OVER -->
    <div class="geld">💰 €<?php echo $totaal_score; ?></div>
    <div class="einde">
        <h1>🎉 Klaar!</h1>
        <p>Jij hebt €<?php echo $totaal_score; ?> verdiend!</p>
        <form method="POST" style="margin-top: 30px;">
            <button type="submit" name="restart" value="1">Opnieuw starten</button>
        </form>
    </div>

<?php else: ?>
    <!-- VRAAG SCHERM -->
    <div class="geld">💰 €<?php echo $totaal_score; ?></div>
    
    <div class="container">
        <div class="vraag-container">
            <div class="vraag">
                <strong>Vraag <?php echo $huidig_vraag_index + 1; ?>/<?php echo count($vragen); ?></strong><br><br>
                <?php echo $huidige_vraag['vraag_tekst']; ?>
            </div>
            
            <?php if (!$resultaat): ?>
                <!-- QUERY INVOER -->
                <form method="POST" class="form-group">
                    <?php if ($student_naam): ?>
                        <input type="hidden" name="naam" value="<?php echo htmlspecialchars($student_naam); ?>">
                    <?php endif; ?>
                    <textarea name="query" placeholder="SELECT * FROM ..." required><?php echo htmlspecialchars(isset($student_query) ? $student_query : ''); ?></textarea>
                    <button type="submit">✓ Controleer</button>
                </form>
            <?php else: ?>
                <!-- RESULTAAT -->
                <div class="resultaat <?php echo $resultaat['score'] == 0 ? 'fout' : ''; ?>">
                    <h2><?php echo $resultaat['score'] == 0 ? '❌ Fout' : '✓ Goed'; ?></h2>
                    <p><strong>Jouw query:</strong></p>
                    <code><?php echo htmlspecialchars($student_query); ?></code>
                    <p><strong>Feedback:</strong></p>
                    <p><?php echo nl2br($resultaat['feedback']); ?></p>
                    <div class="score-display">+€<?php echo $resultaat['score']; ?></div>
                </div>
                
                <form method="POST" class="volgende-btn">
                    <button type="submit" name="volgende" value="1">
                        <?php echo $huidig_vraag_index + 1 < count($vragen) ? 'Volgende vraag →' : 'Klaar!'; ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

<?php endif; ?>

</body>
</html>