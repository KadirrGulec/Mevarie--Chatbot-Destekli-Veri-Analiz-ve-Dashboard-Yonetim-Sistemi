<?php
/**
 * MEVARİE HR Sistemi - Ana Konfigürasyon Dosyası
 * Bu dosya uygulamanın temel ayarlarını ve yardımcı fonksiyonlarını içerir
 */

// Oturum başlat
session_start();

// Türkiye saati
date_default_timezone_set('Europe/Istanbul');

// Uygulama ayarları
define('APP_NAME', 'Mevarie');
define('APP_URL', 'http://localhost/mevarie');

// Hata gösterimi (geliştirme için açık)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı dosyasını dahil et
require_once 'database.php';

// URL oluşturma fonksiyonu
function base_url($path = '') {
    return APP_URL . '/' . $path;
}

/**
 * Güvenlik fonksiyonu - kullanıcı verilerini temizler
 * @param string $data - Temizlenecek veri
 * @return string - Temizlenmiş veri
 */
function clean_input($data) {
    global $conn;
    $data = trim($data);                          // Boşlukları kaldır
    $data = stripslashes($data);                  // Slash'ları kaldır
    $data = htmlspecialchars($data);              // HTML karakterlerini temizle
    return mysqli_real_escape_string($conn, $data); // SQL saldırılarını önle
}
?>