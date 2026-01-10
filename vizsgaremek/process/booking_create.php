<?php
require_once "../config/database.php";
$stmt=$pdo->prepare("INSERT INTO bookings(service_id,customer_name,email,phone,booking_date,booking_time) VALUES (?,?,?,?,?,?)");
$stmt->execute([$_POST['service_id'],$_POST['name'],$_POST['email'],$_POST['phone'],$_POST['date'],$_POST['time']]);
header("Location: ../public/booking.php?success=1");
exit;
