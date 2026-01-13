<?php
session_start();

// Database verbinding
require_once 'db_connect.php';

// Test gebruikers
$test_users = [
    // Studenten
    'student1' => ['password' => 'test123', 'role' => 'student', 'naam' => 'Jan Jansen', 'klas' => 'HBO-ICT 1A'],
    'student2' => ['password' => 'test123', 'role' => 'student', 'naam' => 'Emma de Vries', 'klas' => 'HBO-ICT 1A'],
    'student3' => ['password' => 'test123', 'role' => 'student', 'naam' => 'Lucas Bakker', 'klas' => 'HBO-ICT 1B'],
    'student4' => ['password' => 'test123', 'role' => 'student', 'naam' => 'Sophie Visser', 'klas' => 'HBO-ICT 1B'],
    
    // Docenten
    'docent1' => ['password' => 'admin123', 'role' => 'docent', 'naam' => 'Dhr. Hendriks', 'klas' => null],
    'docent2' => ['password' => 'admin123', 'role' => 'docent', 'naam' => 'Mevr. de Jong', 'klas' => null],
];

$error = '';
$success = '';

// Login verwerken
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (isset($test_users[$username]) && $test_users[$username]['password'] === $password) {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $test_users[$username]['role'];
        $_SESSION['naam'] = $test_users[$username]['naam'];
        $_SESSION['klas'] = $test_users[$username]['klas'];
        
        // Voeg student toe aan Gebruiker tabel als nog niet bestaat
        if ($_SESSION['role'] === 'student') {
            $naam = $test_users[$username]['naam'];
            $klas = $test_users[$username]['klas'];
            
            // Check of gebruiker al bestaat
            $check_stmt = $conn->prepare("SELECT Gebruiker_id FROM Gebruiker WHERE naam = ?");
            $check_stmt->bind_param("s", $naam);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                // Voeg nieuwe gebruiker toe
                $insert_stmt = $conn->prepare("INSERT INTO Gebruiker (naam, Tijd, Score, Klas) VALUES (?, 0, 0, ?)");
                $insert_stmt->bind_param("ss", $naam, $klas);
                $insert_stmt->execute();
            }
        }
        
        // Redirect naar juiste pagina
        if ($_SESSION['role'] === 'docent') {
            header('Location: docent_dashboard.php');
        } else {
            header('Location: student_timer.php');
        }
        exit;
    } else {
        $error = 'Ongeldige gebruikersnaam of wachtwoord';
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    $success = 'U bent uitgelogd';
    session_start();
}

// Als al ingelogd, redirect
if (isset($_SESSION['user'])) {
    if ($_SESSION['role'] === 'docent') {
        header('Location: docent_dashboard.php');
    } else {
        header('Location: student_timer.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timer Systeem - Login</title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 440px;
        }
        
        h1 {
            margin: 0 0 10px 0;
            color: #1e293b;
            font-size: 32px;
            text-align: center;
        }
        
        .subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 30px;
            font-size: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #475569;
            font-weight: 600;
            font-size: 14px;
        }
        
        input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
            font-size: 14px;
        }
        
        .success-message {
            background: #dcfce7;
            color: #166534;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #16a34a;
            font-size: 14px;
        }
        
        .test-accounts {
            margin-top: 30px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            border: 2px dashed #cbd5e1;
        }
        
        .test-accounts h3 {
            margin: 0 0 15px 0;
            color: #1e293b;
            font-size: 16px;
        }
        
        .account-list {
            font-size: 13px;
            color: #475569;
            line-height: 1.8;
        }
        
        .account-list strong {
            color: #1e293b;
            font-family: 'Courier New', monospace;
        }
        
        .divider {
            margin: 15px 0;
            border-top: 1px solid #cbd5e1;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>⏱️ Timer Systeem</h1>
        <div class="subtitle">Log in om te beginnen</div>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Gebruikersnaam</label>
                <input type="text" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Wachtwoord</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" name="login">Inloggen</button>
        </form>
        
        <div class="test-accounts">
            <h3>🧪 Test Accounts</h3>
            <div class="account-list">
                <strong>Studenten:</strong><br>
                • student1 / test123 (Jan Jansen - 1A)<br>
                • student2 / test123 (Emma de Vries - 1A)<br>
                • student3 / test123 (Lucas Bakker - 1B)<br>
                • student4 / test123 (Sophie Visser - 1B)
                
                <div class="divider"></div>
                
                <strong>Docenten:</strong><br>
                • docent1 / admin123 (Dhr. Hendriks)<br>
                • docent2 / admin123 (Mevr. de Jong)
            </div>
        </div>
    </div>
</body>
</html>