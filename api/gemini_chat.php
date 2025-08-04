<?php
// Oturum başlat
session_start();

// Gerekli dosyaları dahil et
require_once '../config/database.php';
require_once '../config/gemini_config.php';

// JSON yanıtı için header
header('Content-Type: application/json');

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Gelen JSON verisini al
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Gerekli alanları kontrol et
if (!$data || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$user_message = $data['message'];
$user_email = $_SESSION['user_email'] ?? 'admin@mevarie.com';

// Chat mesajları tablosunu oluştur
$create_table_sql = "CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_bot TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $create_table_sql);

// Önceki mesajları al (son 10 mesaj)
$conversation_sql = "SELECT message, is_bot FROM chat_messages 
                     WHERE user_email = ? 
                     ORDER BY created_at DESC 
                     LIMIT 10";
$stmt = mysqli_prepare($conn, $conversation_sql);
mysqli_stmt_bind_param($stmt, "s", $user_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$conversation_history = [];
while ($row = mysqli_fetch_assoc($result)) {
    $conversation_history[] = $row;
}
$conversation_history = array_reverse($conversation_history);

// Bot için sistem mesajı
$system_message = "Sen MEVARİE isimli bir insan kaynakları yönetim sisteminin yardımcı asistanısın. 
Görevin kullanıcılara personel yönetimi, raporlama ve sistem kullanımı konularında yardımcı olmak. 
Yanıtların kısa, net ve profesyonel olmalı. 
Türkçe karakterleri doğru kullan. 
Eğer bir konuda bilgin yoksa, kullanıcıyı ilgili menüye yönlendir.
Önceki mesajları hatırla ve tutarlı yanıtlar ver.";

// Gemini için mesaj geçmişini hazırla
$conversation_text = $system_message . "\n\n";
foreach ($conversation_history as $msg) {
    $role = $msg['is_bot'] ? 'Asistan' : 'Kullanıcı';
    $conversation_text .= $role . ": " . $msg['message'] . "\n";
}
$conversation_text .= "Kullanıcı: " . $user_message . "\nAsistan:";

// Gemini API'sine istek gönder
$ch = curl_init(GEMINI_API_URL . '?key=' . GEMINI_API_KEY);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'contents' => [
        [
            'parts' => [
                [
                    'text' => $conversation_text
                ]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => GEMINI_TEMPERATURE,
        'maxOutputTokens' => GEMINI_MAX_TOKENS
    ]
]));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Hata kontrolü
if ($http_code !== 200) {
    error_log("Gemini API Error - HTTP Code: " . $http_code);
    error_log("Gemini API Error - Response: " . $response);
    if ($curl_error) {
        error_log("Gemini API Error - CURL Error: " . $curl_error);
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Gemini API error',
        'details' => $response
    ]);
    exit;
}

$response_data = json_decode($response, true);

// Yanıt formatını kontrol et
if (!isset($response_data['candidates'][0]['content']['parts'][0]['text'])) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid response format',
        'details' => $response
    ]);
    exit;
}

$bot_message = $response_data['candidates'][0]['content']['parts'][0]['text'];

// Mesajları veritabanına kaydet
$stmt = mysqli_prepare($conn, "INSERT INTO chat_messages (user_email, message, is_bot) VALUES (?, ?, ?)");

// Kullanıcı mesajını kaydet
mysqli_stmt_bind_param($stmt, "ssi", $user_email, $user_message, $is_bot_0);
$is_bot_0 = 0;
mysqli_stmt_execute($stmt);

// Bot mesajını kaydet
mysqli_stmt_bind_param($stmt, "ssi", $user_email, $bot_message, $is_bot_1);
$is_bot_1 = 1;
mysqli_stmt_execute($stmt);

// Yanıtı döndür
echo json_encode([
    'success' => true,
    'reply' => $bot_message,
    'conversation_length' => count($conversation_history) + 2
]);
?> 