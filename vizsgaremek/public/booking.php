<?php require_once "../config/database.php"; ?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Időpontfoglalás</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0 text-center">Yumeiho Időpontfoglalás</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($_GET['success'])): ?>
                            <div class="alert alert-success">Sikeres foglalás! Hamarosan értesítjük.</div>
                        <?php endif; ?>

                        <form method="POST" action="../process/booking_create.php">
                            <label class="form-label">Válasszon szolgáltatást:</label>
                            <select name="service_id" class="form-select mb-3">
                                <?php foreach($pdo->query("SELECT * FROM services") as $s){ 
                                    echo "<option value='{$s['id']}'>".htmlspecialchars($s['name'])." ({$s['price']} Ft)</option>"; 
                                } ?>
                            </select>
                            
                            <div class="mb-3">
                                <input name="name" class="form-control" placeholder="Teljes név" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                                <div class="col"><input name="phone" class="form-control" placeholder="Telefon"></div>
                            </div>
                            <div class="row mb-4">
                                <div class="col"><input type="date" name="date" class="form-control" required></div>
                                <div class="col">
                                <label class="form-label text-muted small">Időpont (24h)</label>
                                <input type="time" name="time" class="form-control" required step="60">
                            </div>
                            </div>
                            <button class="btn btn-primary w-100 py-2">Foglalás beküldése</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>