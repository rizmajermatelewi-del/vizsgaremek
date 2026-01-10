<?php
require_once "../includes/auth.php";
require_once "../config/database.php";
$stmt=$pdo->prepare("UPDATE bookings SET status=? WHERE id=?");
$stmt->execute([$_GET['s'],$_GET['id']]);
header("Location: dashboard.php");
