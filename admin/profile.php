<?php
require_once "../config/database.php";
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: login.php'); exit; }

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = 1"); // Feltételezve, hogy az admin ID-ja 1
    if ($stmt->execute([$new_pass])) {
        $message = "Jelszó sikeresen módosítva!";
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Profil</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4 card shadow p-4">
                <h4>Admin jelszó módosítása</h4>
                <?php if($message): ?> <div class="alert alert-success"><?= $message ?></div> <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>Új jelszó</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Mentés</button>
                    <a href="dashboard.php" class="btn btn-link w-100 mt-2 text-decoration-none">Vissza</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>