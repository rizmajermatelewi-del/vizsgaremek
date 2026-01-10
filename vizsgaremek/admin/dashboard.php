<?php  
require_once "../config/database.php"; 

session_start();
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

$pendingCount = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin Panel</title>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Foglalások Kezelése</h2>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Kijelentkezés</a>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body text-center">
                        <h5>Összes foglalás</h5>
                        <h2 class="display-4"><?= $totalBookings ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-warning text-dark shadow">
                    <div class="card-body text-center">
                        <h5>Függőben lévő</h5>
                        <h2 class="display-4"><?= $pendingCount ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Ügyfél</th>
                            <th>Szolgáltatás</th>
                            <th>Időpont</th>
                            <th>Státusz</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    $sql = "SELECT b.*, s.name service, 
            TIME_FORMAT(b.booking_time, '%H:%i') as formatted_time 
            FROM bookings b 
            JOIN services s ON b.service_id=s.id 
            ORDER BY b.booking_date DESC";

    foreach($pdo->query($sql) as $b): ?>
        <tr>
            <td><?= htmlspecialchars($b['customer_name']) ?></td>
            <td><?= htmlspecialchars($b['service']) ?></td>
            <td>
                <?= htmlspecialchars($b['booking_date']) ?> 
                <span class="badge bg-dark"><?= htmlspecialchars($b['formatted_time']) ?></span>
            </td>
            <td><?= htmlspecialchars($b['status']) ?></td>
            <td>
                <a href="update.php?id=<?= $b['id'] ?>&s=approved" class="btn btn-success btn-sm">OK</a>
                <a href="update.php?id=<?= $b['id'] ?>&s=rejected" class="btn btn-danger btn-sm">X</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
abcd
