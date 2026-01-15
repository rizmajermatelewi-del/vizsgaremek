<?php
require_once "../config/database.php";
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: login.php'); exit; }

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: dashboard.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) { die("A foglalás nem található."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE bookings SET booking_date = ?, booking_time = ?, customer_name = ?, phone = ? WHERE id = ?");
    if ($stmt->execute([$_POST['date'], $_POST['time'], $_POST['name'], $_POST['phone'], $id])) {
        header("Location: dashboard.php?msg=updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Szerkesztés</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 row card shadow p-4 flex-row">
                <div class="col-md-6 border-end">
                    <h4 class="mb-4">Adatok módosítása</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Ügyfél neve</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($booking['customer_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Dátum</label>
                            <input type="date" name="date" id="edit_date" class="form-control" value="<?= $booking['booking_date'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Időpont</label>
                            <input type="time" name="time" class="form-control" value="<?= substr($booking['booking_time'], 0, 5) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Telefonszám</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($booking['phone']) ?>" required>
                        </div>
                        <button class="btn btn-primary w-100">Mentés</button>
                    </form>
                </div>
                <div class="col-md-6 px-4">
                    <h5 class="text-danger">Foglalt időpontok ezen a napon:</h5>
                    <ul id="occupied_slots" class="list-group mt-3">
                        <li class="list-group-item small text-muted">Válassz dátumot...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>
        function checkSlots() {
            let date = document.getElementById('edit_date').value;
            fetch(`../public/check_slots.php?date=${date}`)
                .then(r => r.json())
                .then(data => {
                    let list = document.getElementById('occupied_slots');
                    list.innerHTML = data.length ? '' : '<li class="list-group-item text-success">Minden időpont szabad!</li>';
                    data.forEach(t => {
                        let li = document.createElement('li');
                        li.className = "list-group-item list-group-item-danger py-1 small";
                        li.textContent = t.substring(0, 5);
                        list.appendChild(li);
                    });
                });
        }
        document.getElementById('edit_date').addEventListener('change', checkSlots);
        window.onload = checkSlots;
    </script>
</body>
</html>