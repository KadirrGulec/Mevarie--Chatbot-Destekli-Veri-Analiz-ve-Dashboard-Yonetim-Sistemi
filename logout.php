<?php
// Oturum başlat
session_start();

// Oturum verilerini temizle
session_unset();
session_destroy();

// Login sayfasına yönlendir
header('Location: views/login.php');
exit;
?> 