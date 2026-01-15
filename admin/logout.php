<?php
// logout.php: pusztítsd el a sessioneket és dobj át az index.php-ra
session_start();


// Megsemmisítjük a sessiont
session_destroy();

// Átirányítás az index.php-ra
header('Location: login.php');
exit;