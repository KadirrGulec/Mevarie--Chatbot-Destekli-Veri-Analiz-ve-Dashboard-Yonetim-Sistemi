<?php
// Gerekli dosyaları dahil et
require_once '../config/database.php';
require_once '../config/openai_config.php';

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
$conversation_history = array_reverse($conversation_history); // Sıralamayı düzelt

// Bot için sistem mesajı
$system_message = "Sen MEVARİE isimli bir insan kaynakları yönetim sisteminin yardımcı asistanısın. 
Görevin kullanıcılara personel yönetimi, raporlama ve sistem kullanımı konularında yardımcı olmak. 
Yanıtların kısa, net ve profesyonel olmalı. 
Türkçe karakterleri doğru kullan. 
Eğer bir konuda bilgin yoksa, kullanıcıyı ilgili menüye yönlendir.
Önceki mesajları hatırla ve tutarlı yanıtlar ver.";

// ChatGPT'ye gönderilecek mesajları hazırla
$messages = [
    [
        "role" => "system",
        "content" => $system_message
    ]
];

// Önceki mesajları ekle
foreach ($conversation_history as $msg) {
    $messages[] = [
        "role" => $msg['is_bot'] ? "assistant" : "user",
        "content" => $msg['message']
    ];
}

// Yeni kullanıcı mesajını ekle
$messages[] = [
    "role" => "user",
    "content" => $user_message
];

// OpenAI API'sine istek gönder
$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . OPENAI_API_KEY
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => OPENAI_MODEL,
    'messages' => $messages,
    'max_tokens' => OPENAI_MAX_TOKENS,
    'temperature' => OPENAI_TEMPERATURE,
    'top_p' => OPENAI_TOP_P,
    'frequency_penalty' => OPENAI_FREQUENCY_PENALTY,
    'presence_penalty' => OPENAI_PRESENCE_PENALTY
]));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Hata kontrolü
if ($http_code !== 200) {
    error_log("ChatGPT API Error - HTTP Code: " . $http_code);
    error_log("ChatGPT API Error - Response: " . $response);
    if ($curl_error) {
        error_log("ChatGPT API Error - CURL Error: " . $curl_error);
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'ChatGPT API error',
        'details' => $response
    ]);
    exit;
}

$response_data = json_decode($response, true);
$bot_message = $response_data['choices'][0]['message']['content'];

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