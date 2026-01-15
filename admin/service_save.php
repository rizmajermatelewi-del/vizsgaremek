<?php
require_once "../config/database.php";
session_start();
if (empty($_SESSION['admin_logged_in'])) { exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO services (name, price, duration) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['price'], $_POST['duration']]);
}
header("Location: services_list.php");