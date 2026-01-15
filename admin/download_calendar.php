<?php
require_once "../config/database.php";

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT b.*, s.name as service_name FROM bookings b JOIN services s ON b.service_id = s.id WHERE b.id = ?");
    $stmt->execute([$_GET['id']]);
    $b = $stmt->fetch();

    if ($b) {
        $date = str_replace('-', '', $b['booking_date']);
        $time = str_replace(':', '', $b['booking_time']);
        $start = $date . 'T' . $time;
        
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="emlekezteto.ics"');

        echo "BEGIN:VCALENDAR\r\n";
        echo "VERSION:2.0\r\n";
        echo "BEGIN:VEVENT\r\n";
        echo "SUMMARY:Masszázs: " . $b['service_name'] . " - " . $b['customer_name'] . "\r\n";
        echo "DTSTART:" . $start . "\r\n";
        echo "DESCRIPTION:Telefonszám: " . $b['phone'] . "\r\n";
        echo "END:VEVENT\r\n";
        echo "END:VCALENDAR\r\n";
        exit;
    }
}