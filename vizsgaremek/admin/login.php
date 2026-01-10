<?php

$show_error = false;
$error_message = '';

if (isset($_GET['error']) && (string)$_GET['error'] === '1') {
    $show_error = true;
    $error_message = 'Hibás belépési adatok';
}

session_start();
// load PDO connection from db/config/database.php if present, otherwise create a local PDO fallback
$dbConfig = __DIR__ . '/config/database.php';

    
    if (file_exists($dbConfig)) {
        require $dbConfig; // expected to provide $pdo
    } else {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=vizsgaremek;charset=utf8mb4', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('DB connection failed.');
        }
    }

    // SQL to check if a user exists (use prepared statement with :username)
    $checkUserSql = 'SELECT id, username, password FROM users WHERE username = :username LIMIT 1';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare($checkUserSql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        header('Location: vizsgaremek/admin/login.php?error=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin Bejelentkezés</title>
    <style>
        body { background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; }
        .login-card { max-width: 400px; width: 100%; margin: auto; }
    </style>
</head>
<body>
    <div class="login-card p-3">
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Adminisztráció</h3>
                
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger py-2 small text-center">
                        Hibás felhasználónév vagy jelszó!
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Felhasználónév</label>
                        <input name="username" class="form-control" placeholder="" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Jelszó</label>
                        <input type="password" name="password" class="form-control" placeholder="" required>
                    </div>
                    <button class="btn btn-primary w-100 py-2 fw-bold">Belépés</button>
                </form>
            </div>
            <div class="card-footer bg-white text-center py-3 border-0">
                <a href="../public/booking.php" class="text-decoration-none small text-muted">← Vissza a foglaláshoz</a>
            </div>
        </div>
    </div>
</body>
</html>
