<?php
require_once "../config/database.php";
session_start();
if (empty($_SESSION['admin_logged_in'])) exit;

// Töröljük a 30 napnál régebbi, elutasított vagy már lezajlott foglalásokat
$pdo->query("DELETE FROM bookings WHERE booking_date < CURDATE() AND (status='rejected' OR status='approved')");

header("Location: dashboard.php?msg=cleaned");