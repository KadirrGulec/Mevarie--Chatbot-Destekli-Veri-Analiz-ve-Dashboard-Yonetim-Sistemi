<?php
session_start();

// Sabit kullanıcı bilgileri
$valid_email = 'mevarie@mail.com';
$valid_password = 'admin123';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === $valid_email && $password === $valid_password) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_email'] = $email;
        header('Location: ../views/dashboard.php');
        exit();
    } else {
        header('Location: ../views/login.php?error=1');
        exit();
    }
} else {
    header('Location: ../views/login.php');
    exit();
}
