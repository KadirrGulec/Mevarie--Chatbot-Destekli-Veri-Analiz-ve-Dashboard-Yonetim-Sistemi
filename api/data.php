<?php
// JSON yanıtı için header
header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';

// Gelen veriyi al
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

// İstatistik verilerini getir
if ($action === 'get_stats') {
    // İstatistik kartları için veriler
    $stats = [
        'total_personel' => 0,
        'aktif_personel' => 0,
        'aylik_gider' => 0,
        'ortalama_maas' => 0,
        'yeni_personel' => 0,
        'verimlilik' => 0
    ];

    // Toplam ve aktif personel sayısı
    $result = mysqli_query($conn, "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN durum = 'Aktif' THEN 1 ELSE 0 END) as aktif
        FROM personel");
    if ($row = mysqli_fetch_assoc($result)) {
        $stats['total_personel'] = (int)$row['total'];
        $stats['aktif_personel'] = (int)$row['aktif'];
    }

    // Aylık gider ve ortalama maaş
    $result = mysqli_query($conn, "SELECT 
        SUM(maas) as total_maas,
        AVG(maas) as avg_maas
        FROM personel 
        WHERE durum = 'Aktif'");
    if ($row = mysqli_fetch_assoc($result)) {
        $stats['aylik_gider'] = (float)$row['total_maas'];
        $stats['ortalama_maas'] = (float)$row['avg_maas'];
    }

    // Son 30 gündeki yeni personel sayısı
    $result = mysqli_query($conn, "SELECT COUNT(*) as yeni 
        FROM personel 
        WHERE giris_tarihi >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    if ($row = mysqli_fetch_assoc($result)) {
        $stats['yeni_personel'] = (int)$row['yeni'];
    }

    // Verimlilik (Aktif/Toplam personel oranı)
    if ($stats['total_personel'] > 0) {
        $stats['verimlilik'] = round(($stats['aktif_personel'] / $stats['total_personel']) * 100);
    }

    // Grafik için kullanılacak alanlar
    $alanlar = [
        'pozisyon' => 'Pozisyon',
        'departman' => 'Departman',
        'sehir' => 'Şehir',
        'cinsiyet' => 'Cinsiyet',
        'maas_aralik' => 'Maaş Aralığı',
        'yas_aralik' => 'Yaş Aralığı',
        'giris_yili' => 'Giriş Yılı'
    ];
    
    $veriGruplari = [];

    // Maaş aralıkları için özel sorgu
    $maas_sql = "SELECT 
        CASE 
            WHEN maas < 10000 THEN '0-10.000₺'
            WHEN maas < 15000 THEN '10.000₺-15.000₺'
            WHEN maas < 20000 THEN '15.000₺-20.000₺'
            WHEN maas < 25000 THEN '20.000₺-25.000₺'
            ELSE '25.000₺+'
        END as deger,
        COUNT(*) as sayi 
        FROM personel 
        GROUP BY 
        CASE 
            WHEN maas < 10000 THEN '0-10.000₺'
            WHEN maas < 15000 THEN '10.000₺-15.000₺'
            WHEN maas < 20000 THEN '15.000₺-20.000₺'
            WHEN maas < 25000 THEN '20.000₺-25.000₺'
            ELSE '25.000₺+'
        END 
        ORDER BY MIN(maas)";

    // Yaş aralıkları için özel sorgu
    $yas_sql = "SELECT 
        CASE 
            WHEN yas < 25 THEN '18-25'
            WHEN yas < 35 THEN '26-35'
            WHEN yas < 45 THEN '36-45'
            ELSE '45+'
        END as deger,
        COUNT(*) as sayi 
        FROM personel 
        GROUP BY 
        CASE 
            WHEN yas < 25 THEN '18-25'
            WHEN yas < 35 THEN '26-35'
            WHEN yas < 45 THEN '36-45'
            ELSE '45+'
        END 
        ORDER BY MIN(yas)";

    foreach ($alanlar as $alan => $alanLabel) {
        $sql = "";
        switch ($alan) {
            case 'maas_aralik':
                $sql = $maas_sql;
                break;
            case 'yas_aralik':
                $sql = $yas_sql;
                break;
            case 'giris_yili':
                $sql = "SELECT YEAR(giris_tarihi) as deger, COUNT(*) as sayi 
                       FROM personel 
                       GROUP BY YEAR(giris_tarihi) 
                       ORDER BY deger DESC";
                break;
            default:
                $sql = "SELECT `$alan` as deger, COUNT(*) as sayi 
                       FROM personel 
                       GROUP BY `$alan` 
                       ORDER BY sayi DESC, deger";
        }

        $result = mysqli_query($conn, $sql);
        $labels = [];
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $labels[] = $row['deger'] ?? 'Belirtilmemiş';
                $data[] = (int)$row['sayi'];
            }
        }
        
        $veriGruplari[$alan] = ['labels' => $labels, 'data' => $data];
    }

    // Son 6 ayın personel değişim trendi
    $trend_sql = "SELECT 
        DATE_FORMAT(giris_tarihi, '%Y-%m') as ay,
        COUNT(*) as giris_sayisi
        FROM personel 
        WHERE giris_tarihi >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(giris_tarihi, '%Y-%m')
        ORDER BY ay";

    $trend_result = mysqli_query($conn, $trend_sql);
    $last_6_months = [];

    // Son 6 ayı oluştur
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $last_6_months[$month] = 0;
    }

    // Verileri doldur
    if ($trend_result) {
        while ($row = mysqli_fetch_assoc($trend_result)) {
            $last_6_months[$row['ay']] = (int)$row['giris_sayisi'];
        }
    }

    $trend_labels = array_map(function($month) {
        return date('F Y', strtotime($month));
    }, array_keys($last_6_months));

    $trend_values = array_values($last_6_months);

    $veriGruplari['trend'] = [
        'labels' => $trend_labels,
        'data' => $trend_values
    ];
    
    echo json_encode(['success' => true, 'stats' => $stats, 'veriGruplari' => $veriGruplari]);
    exit;
}

// Personel ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($action)) {
    // Gerekli alanlar
    $required_fields = ['ad', 'soyad', 'cinsiyet', 'yas', 'pozisyon', 'departman', 'maas', 'giris_tarihi', 'sehir', 'email', 'durum'];
    $values = [];
    $missing = [];

    // Gerekli alanları kontrol et
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $missing[] = $field;
        } else {
            $values[$field] = mysqli_real_escape_string($conn, trim($_POST[$field]));
        }
    }

    // Eksik alan kontrolü
    if (!empty($missing)) {
        echo json_encode(['success' => false, 'error' => 'Eksik alanlar: ' . implode(', ', $missing)]);
        exit;
    }

    // Email format kontrolü
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Geçersiz email formatı']);
        exit;
    }

    // Yaş kontrolü
    if (!is_numeric($values['yas']) || $values['yas'] < 16 || $values['yas'] > 100) {
        echo json_encode(['success' => false, 'error' => 'Geçersiz yaş']);
        exit;
    }

    // Email benzersizlik kontrolü
    $check_email = mysqli_query($conn, "SELECT id FROM personel WHERE email = '{$values['email']}'");
    if (mysqli_num_rows($check_email) > 0) {
        echo json_encode(['success' => false, 'error' => 'Bu email adresi zaten kullanılıyor']);
        exit;
    }

    // Veritabanına kaydet
    $fields = implode('`, `', array_keys($values));
    $values_str = implode("', '", $values);
    $sql = "INSERT INTO personel (`$fields`) VALUES ('$values_str')";

    if (mysqli_query($conn, $sql)) {
        $id = mysqli_insert_id($conn);
        echo json_encode(['success' => true, 'id' => $id]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Veritabanı hatası: ' . mysqli_error($conn)]);
    }
    exit;
}

// Personel silme işlemi
if ($action === 'delete') {
    $id = intval($input['id'] ?? 0);
    if ($id > 0) {
        $res = mysqli_query($conn, "DELETE FROM personel WHERE id = $id");
        if ($res) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => 'Veritabanı hatası']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Geçersiz ID']);
        exit;
    }
}

// Personel güncelleme işlemi
if ($action === 'update') {
    $id = intval($input['id'] ?? 0);
    if ($id > 0) {
        $fields = ['ad','soyad','cinsiyet','yas','pozisyon','departman','maas','giris_tarihi','sehir','email','durum'];
        $set = [];
        
        // Güncellenecek alanları hazırla
        foreach ($fields as $f) {
            if (isset($input[$f])) {
                $val = mysqli_real_escape_string($conn, $input[$f]);
                $set[] = "`