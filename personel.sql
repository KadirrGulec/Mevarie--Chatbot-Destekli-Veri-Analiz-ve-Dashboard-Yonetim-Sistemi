CREATE TABLE IF NOT EXISTS `personel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `cinsiyet` enum('Erkek','Kadın') NOT NULL,
  `yas` int(3) NOT NULL,
  `pozisyon` varchar(100) NOT NULL,
  `departman` varchar(100) NOT NULL,
  `maas` decimal(10,2) NOT NULL,
  `giris_tarihi` date NOT NULL,
  `sehir` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `durum` enum('Aktif','Pasif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 