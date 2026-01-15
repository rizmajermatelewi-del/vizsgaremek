<?php
require_once "../config/database.php";
session_start();

// Csak admin törölhet
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: dashboard.php");
exit;