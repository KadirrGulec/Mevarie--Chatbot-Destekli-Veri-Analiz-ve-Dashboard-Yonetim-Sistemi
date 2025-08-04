<?php
/**
 * MEVARİE HR Sistemi - Veritabanı Konfigürasyonu
 * Bu dosya veritabanı bağlantı ayarlarını içerir
 */

// Veritabanı bağlantı ayarları
define('DB_SERVER', 'localhost');        // Sunucu adresi
define('DB_USERNAME', 'mevarie');        // Kullanıcı adı
define('DB_PASSWORD', '');               // Şifre (XAMPP'te boş)
define('DB_NAME', 'yazilim_sirketi');    // Veritabanı adı

// Veritabanına bağlan
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, 3306);

// Bağlantıyı kontrol et
if (!$conn) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}

// Türkçe karakterler için
mysqli_set_charset($conn, "utf8mb4");
?>