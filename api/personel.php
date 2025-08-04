<?php
require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT');
header('Access-Control-Allow-Headers: Content-Type');

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'get':
        // Tek bir personel kaydını getir
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID gerekli']);
            exit;
        }

        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM personel WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode(['success' => true, 'personel' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Personel bulunamadı']);
        }
        break;

    case 'add':
        // Yeni personel ekle
        if (!isset($_POST['ad']) || !isset($_POST['soyad']) || !isset($_POST['email'])) {
            echo json_encode(['success' => false, 'message' => 'Gerekli alanlar eksik']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO personel (ad, soyad, cinsiyet, yas, pozisyon, departman, maas, giris_tarihi, sehir, email, durum) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("sssississss", 
            $_POST['ad'],
            $_POST['soyad'],
            $_POST['cinsiyet'],
            $_POST['yas'],
            $_POST['pozisyon'],
            $_POST['departman'],
            $_POST['maas'],
            $_POST['giris_tarihi'],
            $_POST['sehir'],
            $_POST['email'],
            $_POST['durum']
        );
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Personel başarıyla eklendi', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Personel eklenirken hata oluştu: ' . $stmt->error]);
        }
        break;

    case 'update':
        // Personel güncelle
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID gerekli']);
            exit;
        }

        $id = intval($_GET['id']);
        $stmt = $conn->prepare("UPDATE personel SET ad=?, soyad=?, cinsiyet=?, yas=?, pozisyon=?, departman=?, maas=?, giris_tarihi=?, sehir=?, email=?, durum=? WHERE id=?");
        
        $stmt->bind_param("sssississssi", 
            $_POST['ad'],
            $_POST['soyad'],
            $_POST['cinsiyet'],
            $_POST['yas'],
            $_POST['pozisyon'],
            $_POST['departman'],
            $_POST['maas'],
            $_POST['giris_tarihi'],
            $_POST['sehir'],
            $_POST['email'],
            $_POST['durum'],
            $id
        );
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Personel başarıyla güncellendi']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Personel güncellenirken hata oluştu: ' . $stmt->error]);
        }
        break;

    case 'delete':
        // Personel sil
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID gerekli']);
            exit;
        }

        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM personel WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Personel başarıyla silindi']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Personel silinirken hata oluştu: ' . $stmt->error]);
        }
        break;

    case 'list':
    default:
        // Tüm personel listesini getir
        $result = mysqli_query($conn, "SELECT * FROM personel ORDER BY id DESC");
        $personel = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $personel[] = $row;
        }
        
        echo json_encode(['success' => true, 'personel' => $personel]);
        break;
}
?> 