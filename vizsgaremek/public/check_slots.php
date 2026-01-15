<?php
require_once "../config/database.php";

// Megnézzük, érkezett-e dátum a kéréssel
$date = $_GET['date'] ?? '';

if ($date) {
    // Lekérjük azokat az időpontokat, amik már foglaltak és nincsenek elutasítva
    $stmt = $pdo->prepare("SELECT booking_time FROM bookings WHERE booking_date = ? AND status != 'rejected'");
    $stmt->execute([$date]);
    
    // Csak a listát adjuk vissza (pl. ["08:00:00", "10:00:00"])
    $takenSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // JSON formátumban küldjük vissza a JavaScriptnek
    header('Content-Type: application/json');
    echo json_encode($takenSlots);
} else {
    echo json_encode([]);
}