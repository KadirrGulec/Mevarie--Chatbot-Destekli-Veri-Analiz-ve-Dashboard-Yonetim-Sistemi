<?php
// Oturum başlatma
function start_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Giriş kontrolü
function check_login() {
    start_session();
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: ../views/login.php');
        exit();
    }
}

// Oturumu sonlandırma (logout)
function logout() {
    start_session();
    session_unset();
    session_destroy();
    header('Location: ../views/login.php');
    exit();
}
