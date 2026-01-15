<?php
require_once "../config/database.php";
session_start();

if (empty($_SESSION['admin_logged_in'])) {
    exit("Bejelentkezés szükséges.");
}

if (isset($_GET['id']) && isset($_GET['s'])) {
    $id = $_GET['id'];
    $status = $_GET['s'];

    // 1. Állapot frissítése
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    // 2. Ha elfogadtuk, küldjünk e-mailt
    if ($status === 'approved') {
        // Lekérjük a foglalás adatait az e-mailhez
        $stmt = $pdo->prepare("SELECT b.*, s.name as service_name FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.id = ?");
        $stmt->execute([$id]);
        $booking = $stmt->fetch();

        if ($booking) {
            $to = $booking['email'];
            $subject = "Visszaigazolás: Időpontfoglalás elfogadva";
            $message = "Kedves " . $booking['customer_name'] . "!\n\n" .
                       "Örömmel értesítjük, hogy a(z) " . $booking['booking_date'] . " " . $booking['booking_time'] . 
                       " időpontra leadott foglalását (" . $booking['service_name'] . ") elfogadtuk.\n\n" .
                       "Várjuk szeretettel!";
            $headers = "From: no-reply@yumeiho.hu";

            // Megjegyzés: Localhoston az e-mail küldéshez beállított SMTP szerver kell (pl. Mailtrap vagy XAMPP sendmail)
            mail($to, $subject, $message, $headers);
        }
    }
}

header("Location: dashboard.php");
exit;