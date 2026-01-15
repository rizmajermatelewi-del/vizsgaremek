<?php
require_once "../config/database.php";
session_start();
if (empty($_SESSION['admin_logged_in'])) { exit; }

$today = date('Y-m-d');
$sql = "SELECT b.*, s.name service FROM bookings b 
        JOIN services s ON b.service_id=s.id 
        WHERE b.booking_date = ? AND b.status = 'approved' 
        ORDER BY b.booking_time ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$today]);
$agenda = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Mai munkaterv</title>
    <style> @media print { .no-print { display: none; } } </style>
</head>
<body class="p-4">
    <div class="container border p-4 bg-white">
        <h2 class="text-center">Napi beosztás - <?= $today ?></h2>
        <hr>
        <?php if(empty($agenda)): ?>
            <p class="text-center">Mára nincs jóváhagyott foglalás.</p>
        <?php else: ?>
            <table class="table table-bordered mt-4">
                <thead class="table-light">
                    <tr>
                        <th>Időpont</th>
                        <th>Név</th>
                        <th>Szolgáltatás</th>
                        <th>Telefon</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($agenda as $row): ?>
                    <tr>
                        <td class="fw-bold"><?= substr($row['booking_time'], 0, 5) ?></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= htmlspecialchars($row['service']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-dark">Nyomtatás / PDF</button>
            <a href="dashboard.php" class="btn btn-link">Vissza</a>
        </div>
    </div>
</body>
</html>