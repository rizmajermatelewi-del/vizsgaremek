<?php  
require_once "../config/database.php"; 

session_start();
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 1. STATISZTIKÁK LEKÉRDEZÉSE
$pendingCount = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(s.price) FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.status='approved'")->fetchColumn() ?: 0;
$popularService = $pdo->query("SELECT s.name FROM bookings b JOIN services s ON b.service_id = s.id GROUP BY s.name ORDER BY COUNT(*) DESC LIMIT 1")->fetchColumn() ?: "Nincs adat";
$todayRemaining = $pdo->query("SELECT COUNT(*) FROM bookings WHERE booking_date = CURDATE() AND status='approved' AND booking_time > CURTIME()")->fetchColumn();

// Vélemények lekérése
$reviews = $pdo->query("SELECT * FROM reviews ORDER BY id DESC LIMIT 5")->fetchAll();

// 2. FOGLALÁSOK LEKÉRDEZÉSE
$sql = "SELECT b.*, s.name service, TIME_FORMAT(b.booking_time, '%H:%i') as formatted_time 
        FROM bookings b JOIN services s ON b.service_id=s.id ORDER BY b.booking_date DESC";
$bookings = $pdo->query($sql)->fetchAll();

function getStatusBadge($status) {
    switch($status) {
        case 'approved': return '<span class="badge bg-success">Jóváhagyva</span>';
        case 'rejected': return '<span class="badge bg-danger">Elutasítva</span>';
        default: return '<span class="badge bg-warning text-dark">Függőben</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin - Vezérlőpult</title>
    <style>
        .card { border: none; border-radius: 12px; }
        .table-container { background: white; border-radius: 12px; padding: 20px; }
        @media (prefers-color-scheme: dark) {
            body { background-color: #121212 !important; color: white !important; }
            .card, .table-container { background-color: #1e1e1e !important; color: white !important; }
            .table { color: white !important; }
            .table-light { background-color: #2d2d2d !important; color: white !important; }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Foglalások Kezelése</h2>
            <div>
                <a href="services_list.php" class="btn btn-outline-primary btn-sm me-1">Szolgáltatások</a>
                <a href="daily_agenda.php" class="btn btn-outline-dark btn-sm me-1">Napi terv</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Kilépés</a>
            </div>
        </div>

        <div class="row mb-4 text-center">
            <div class="col-md-3 col-6 mb-2">
                <div class="card bg-primary text-white shadow-sm p-3">
                    <small>Összes</small><h4><?= $totalBookings ?></h4>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="card bg-warning text-dark shadow-sm p-3">
                    <small>Függőben</small><h4><?= $pendingCount ?></h4>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="card bg-success text-white shadow-sm p-3">
                    <small>Bevétel</small><h4><?= number_format($totalRevenue, 0, ',', ' ') ?> Ft</h4>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="card bg-dark text-white shadow-sm p-3">
                    <small>Mára hátra van</small><h4><?= $todayRemaining ?> fő</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm p-3 bg-white mb-4">
                    <h5 class="text-center mb-3">Statisztika</h5>
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="card shadow-sm bg-white p-3 mb-4">
                    <h5>Vélemények</h5>
                    <?php foreach($reviews as $r): ?>
                        <div class="border-bottom py-2 small">
                            <strong><?= htmlspecialchars($r['customer_name']) ?></strong> 
                            <span class="text-warning"><?= str_repeat('★', $r['rating']) ?></span>
                            <p class="mb-0 text-muted"><?= htmlspecialchars($r['comment']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-8">
                <input type="text" id="searchInput" class="form-control mb-3" placeholder="Keresés az ügyfelek között...">
                <div class="table-container shadow-sm">
                    <table class="table table-hover align-middle">
                        <thead class="table-light"><tr><th>Ügyfél</th><th>Időpont</th><th>Műveletek</th></tr></thead>
                        <tbody>
                            <?php foreach($bookings as $b): $isToday = ($b['booking_date'] == date('Y-m-d')); ?>
                            <tr class="booking-row <?= $isToday ? 'table-info' : '' ?>">
                                <td>
                                    <strong class="customer-name"><?= htmlspecialchars($b['customer_name']) ?></strong><br>
                                    <small><?= htmlspecialchars($b['service']) ?></small>
                                </td>
                                <td><?= $b['booking_date'] ?><br><b><?= $b['formatted_time'] ?></b></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="update.php?id=<?= $b['id'] ?>&s=approved" class="btn btn-outline-success btn-sm">✔</a>
                                        <a href="edit_booking.php?id=<?= $b['id'] ?>" class="btn btn-outline-secondary btn-sm">✏️</a>
                                        <a href="update.php?id=<?= $b['id'] ?>&s=pending" class="btn btn-outline-primary btn-sm">🕒</a>
                                        <a href="update.php?id=<?= $b['id'] ?>&s=rejected" class="btn btn-outline-warning btn-sm">✘</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('statusChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Jóváhagyva', 'Függőben', 'Elutasítva'],
                datasets: [{
                    data: [<?= $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='approved'")->fetchColumn() ?>, <?= $pendingCount ?>, <?= $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='rejected'")->fetchColumn() ?>],
                    backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } }, cutout: '70%' }
        });
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            document.querySelectorAll('.booking-row').forEach(row => {
                let name = row.querySelector('.customer-name').textContent.toLowerCase();
                row.style.display = name.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>