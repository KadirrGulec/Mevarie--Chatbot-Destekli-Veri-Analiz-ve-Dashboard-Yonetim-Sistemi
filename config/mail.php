<?php
// Mail ayarları
return [
    'smtp_host' => 'smtp.gmail.com',  // SMTP sunucusu (örnek: Gmail)
    'smtp_port' => 587,               // SMTP portu
    'smtp_secure' => 'tls',           // Güvenlik protokolü (tls veya ssl)
    'smtp_auth' => true,              // SMTP kimlik doğrulama
    'smtp_username' => '',            // SMTP kullanıcı adı (e-posta adresi)
    'smtp_password' => '',            // SMTP şifresi (uygulama şifresi)
    'from_email' => '',              // Gönderen e-posta adresi
    'from_name' => 'MEVARİE',        // Gönderen adı
    'reply_to' => '',                // Yanıt adresi (boş bırakılırsa from_email kullanılır)
];