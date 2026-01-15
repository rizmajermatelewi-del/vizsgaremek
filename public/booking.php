<?php require_once "../config/database.php"; ?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Időpont Foglalás - Masszázs Szalon</title>
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .booking-card { max-width: 550px; margin: 40px auto; border-radius: 20px; border: none; overflow: hidden; }
        .card-header { background: #0d6efd; color: white; text-align: center; padding: 25px; border: none; }
        .form-label { font-weight: 600; color: #495057; }
        .btn-submit { padding: 12px; font-weight: bold; border-radius: 10px; transition: 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3); }
    </style>
</head>
<body>
    <div class="container">
        <div class="card booking-card shadow-lg">
            <div class="card-header">
                <h2 class="mb-0">Időpont Foglalás</h2>
                <small>Válassza ki a kívánt szolgáltatást és időpontot!</small>
            </div>
            
            <div class="card-body p-4">
                <?php if(isset($_GET['success'])): ?>
                    <div class="alert alert-success shadow-sm border-0 mb-4">
                        <h5 class="alert-heading">✨ Sikeres foglalás!</h5>
                        <p>Foglalását rögzítettük, hamarosan értesítjük a részletekről.</p>
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=Masszázs+időpont&details=Köszönjük+a+foglalást!" 
                               target="_blank" class="btn btn-success btn-sm">
                               📅 Hozzáadás Google Naptárhoz
                            </a>
                            <div id="countdown" class="badge bg-dark p-2 text-wrap">Várjuk szeretettel!</div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['error']) && $_GET['error'] == 'taken'): ?>
                    <div class="alert alert-danger border-0 shadow-sm">
                        <strong>Hiba!</strong> Ezt az időpontot éppen most foglalták le. Kérjük, válasszon másikat!
                    </div>
                <?php endif; ?>

                <form action="../process/booking_create.php" method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Teljes név</label>
                            <input type="text" name="name" class="form-control" required placeholder="Gipsz Jakab">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">E-mail cím</label>
                        <input type="email" name="email" class="form-control" required placeholder="pelda@email.hu">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefonszám</label>
                        <input type="tel" name="phone" class="form-control" required placeholder="+36 30 123 4567">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Választható szolgáltatás</label>
                        <select name="service_id" class="form-select" required>
                            <option value="" disabled selected>Válasszon szolgáltatást...</option>
                            <?php
                            $services = $pdo->query("SELECT * FROM services")->fetchAll();
                            foreach($services as $s) {
                                echo "<option value='{$s['id']}'>{$s['name']} (".number_format($s['price'], 0, ',', ' ')." Ft)</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dátum</label>
                            <input type="date" name="date" id="booking_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Időpont</label>
                            <select name="time" id="booking_time" class="form-select" required disabled>
                                <option value="">Válasszon dátumot!</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-submit w-100 shadow-sm">
                            Foglalás véglegesítése
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    

    <script>
    // DINAMIKUS IDŐPONT ELLENŐRZÉS (AJAX)
    document.getElementById('booking_date').addEventListener('change', function() {
        let date = this.value;
        let timeSelect = document.getElementById('booking_time');
        
        timeSelect.disabled = false;
        timeSelect.innerHTML = '<option value="">Időpontok keresése...</option>';

        // Lekérjük a foglalt időpontokat a check_slots.php-tól
        fetch(`check_slots.php?date=${date}`)
            .then(res => res.json())
            .then(takenSlots => {
                // Definiáljuk a nyitvatartási időket
                const availableTimes = ["08:00", "09:00", "10:00", "11:00", "13:00", "14:00", "15:00", "16:00"];
                timeSelect.innerHTML = '';
                
                availableTimes.forEach(time => {
                    // Megnézzük, a takenSlots tömb tartalmazza-e az adott órát
                    let isTaken = takenSlots.some(s => s.startsWith(time));
                    
                    let option = document.createElement('option');
                    option.value = time;
                    option.disabled = isTaken;
                    option.text = isTaken ? `${time} (Foglalt)` : time;
                    
                    if(isTaken) {
                        option.style.color = '#adb5bd';
                    }
                    timeSelect.appendChild(option);
                });
            })
            .catch(err => {
                timeSelect.innerHTML = '<option value="">Hiba az adatok lekérésekor!</option>';
            });
    });
    </script>
</body>
</html>