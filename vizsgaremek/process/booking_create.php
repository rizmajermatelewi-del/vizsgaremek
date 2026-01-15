<?php
require_once "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $service_id = $_POST['service_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // 1. Ütközés ellenőrzése (biztonsági okokból)
    $stmt = $pdo->prepare("SELECT id FROM bookings WHERE booking_date = ? AND booking_time = ? AND status != 'rejected'");
    $stmt->execute([$date, $time]);
    
    if ($stmt->fetch()) {
        header("Location: ../public/booking.php?error=taken");
        exit;
    }

    // 2. Foglalás mentése
    $insert = $pdo->prepare("INSERT INTO bookings (customer_name, email, phone, service_id, booking_date, booking_time, status) 
                            VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    
    if ($insert->execute([$name, $email, $phone, $service_id, $date, $time])) {
        header("Location: ../public/booking.php?success=1");
    } else {
        echo "Hiba történt a mentés során.";
    }
}