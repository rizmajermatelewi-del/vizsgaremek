<?php
require_once "../config/database.php";
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: login.php'); exit; }

$services = $pdo->query("SELECT * FROM services")->fetchAll();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Szolgáltatások Kezelése</title>
</head>
<body class="bg-light p-4">
    <div class="container card shadow p-4">
        <div class="d-flex justify-content-between mb-3">
            <h3>Szolgáltatások és Árak</h3>
            <a href="dashboard.php" class="btn btn-secondary btn-sm">Vissza a Dashboardra</a>
        </div>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Szolgáltatás neve</th>
                    <th>Ár (Ft)</th>
                    <th>Időtartam (perc)</th>
                    <th>Művelet</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($services as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= number_format($s['price'], 0, ',', ' ') ?> Ft</td>
                    <td><?= $s['duration'] ?> perc</td>
                    <td>
                        <a href="service_delete.php?id=<?= $s['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Biztosan törlöd?')">Törlés</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-4 p-3 bg-white border rounded">
            <h5>Új szolgáltatás hozzáadása</h5>
            <form action="service_save.php" method="POST" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="name" class="form-control" placeholder="Pl: Talpmasszázs" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="price" class="form-control" placeholder="Ár (Ft)" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="duration" class="form-control" placeholder="Perc" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">Mentés</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>