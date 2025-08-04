<?php
require_once '../includes/session.php';
check_login();
require_once '../config/database.php';

// --- TÜM ALANLAR İÇİN GRUPLAMA ---
$alanlar = [
    'pozisyon' => 'Pozisyon',
    'departman' => 'Departman',
    'sehir' => 'Şehir',
    'cinsiyet' => 'Cinsiyet',
    'maas_aralik' => 'Maaş Aralığı',
    'yas_aralik' => 'Yaş Aralığı',
    'giris_yili' => 'Giriş Yılı'
];

// Personel listesini çek
$sql = "SELECT * FROM personel ORDER BY id DESC LIMIT 10";
$result = mysqli_query($conn, $sql);
$personel_listesi = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $personel_listesi[] = $row;
    }
}

// İstatistik verileri
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
    $stats['total_personel'] = $row['total'];
    $stats['aktif_personel'] = $row['aktif'];
}

// Aylık gider ve ortalama maaş
$result = mysqli_query($conn, "SELECT 
    SUM(maas) as total_maas,
    AVG(maas) as avg_maas
    FROM personel 
    WHERE durum = 'Aktif'");
if ($row = mysqli_fetch_assoc($result)) {
    $stats['aylik_gider'] = $row['total_maas'];
    $stats['ortalama_maas'] = $row['avg_maas'];
}

// Son 30 gündeki yeni personel sayısı
$result = mysqli_query($conn, "SELECT COUNT(*) as yeni 
    FROM personel 
    WHERE giris_tarihi >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
if ($row = mysqli_fetch_assoc($result)) {
    $stats['yeni_personel'] = $row['yeni'];
}

// Verimlilik (Aktif/Toplam personel oranı)
if ($stats['total_personel'] > 0) {
    $stats['verimlilik'] = round(($stats['aktif_personel'] / $stats['total_personel']) * 100);
}

// --- TÜM ALANLAR İÇİN GRUPLAMA ---
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
$trend_data = [];
$current_month = date('Y-m');
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

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MEVARİE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { margin: 0; background: #151515; color: #fff; font-family: 'Segoe UI', Arial, sans-serif; }
        
        /* Ana içerik stilleri */
        .container { padding: 40px; }
        
        /* Kontrol paneli */
        .control-panel {
            background: #1a1c23;
            padding: 24px;
            border-radius: 16px;
            margin-bottom: 32px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }
        
        .control-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .control-group label {
            color: #ffb396;
            font-size: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .control-group select {
            background: #2a2d35;
            border: 2px solid #343842;
            color: #fff;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            cursor: pointer;
            min-width: 150px;
        }
        
        .control-group select:focus {
            outline: none;
            border-color: #ffb396;
            box-shadow: 0 0 0 3px rgba(255, 179, 150, 0.1);
        }
        
        .control-group select:hover {
            border-color: #4a4d57;
        }
        
        .btn {
            background: #ffb396;
            color: #1a1c23;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .btn:hover {
            background: #ffa07a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 179, 150, 0.3);
        }
        
        /* İstatistik kartları */
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
            gap: 24px; 
            margin-bottom: 32px; 
        }
        
        .stat-card { 
            background: #1a1c23; 
            padding: 24px; 
            border-radius: 12px; 
            box-shadow: 0 2px 16px rgba(0,0,0,0.12); 
        }
        
        .stat-card .title { 
            color: #a0a3b1; 
            font-size: 0.9rem; 
            margin-bottom: 8px; 
        }
        
        .stat-card .value { 
            color: #fff; 
            font-size: 1.8rem; 
            font-weight: bold;
            margin-bottom: 12px; 
        }
        
        .stat-card .change { 
            display: flex;
            align-items: center;
            gap: 6px; 
            font-size: 0.9rem; 
        }
        
        .stat-card .change.positive { color: #4caf50; }
        .stat-card .change.negative { color: #f44336; }
        
        /* Grafik kartları */
        .charts-grid { 
            display: grid;
            gap: 24px;
        }
        
        /* İlk iki grafik yan yana */
        .charts-grid .chart-card:nth-child(1),
        .charts-grid .chart-card:nth-child(2) {
            grid-column: span 1;
        }
        
        /* Responsive düzen */
        @media (min-width: 1200px) {
            .charts-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 1199px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            .charts-grid .chart-card {
                grid-column: 1 / -1;
            }
        }
        
        .chart-card {
            background: #1a1c23; 
            padding: 24px; 
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.12); 
        }
        
        .chart-card .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .chart-card .chart-header h3 { 
            color: #ffb396;
            margin: 0;
            font-size: 1.2rem; 
        }
        
        .chart-card .chart-controls {
            display: flex;
            gap: 12px;
        }
        
        .chart-card .chart-controls select {
            background: #2a2d35;
            border: 1px solid #343842;
            color: #fff;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .chart-container { 
            position: relative; 
            height: 300px;
            margin-bottom: 16px;
        }
        
        .chart-summary {
            color: #a0a3b1;
            font-size: 0.9rem;
            padding: 12px;
            background: #2a2d35;
            border-radius: 6px;
        }

        /* Mobil menü butonu */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1a1c23;
            border: 1px solid #2a2d35;
            color: #ffb396;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1001;
        }

        @media (max-width: 768px) {
            .menu-toggle { display: block; }
            .container { padding: 20px; }
            .charts-grid { grid-template-columns: 1fr; }
            .control-panel { flex-direction: column; align-items: stretch; }
            .control-group { flex-direction: column; align-items: stretch; }
            .control-group select { width: 100%; }
        }

        /* Chatbot Widget Styles */
        .chatbot-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            background: #1a1c23;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
            z-index: 1000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .chatbot-widget.minimized {
            transform: translateY(calc(100% - 60px));
        }

        .chatbot-header {
            background: #2a2d35;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            border-bottom: 1px solid #343842;
        }

        .chatbot-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ffb396;
        }

        .chatbot-title i {
            font-size: 20px;
        }

        .chatbot-controls button {
            background: none;
            border: none;
            color: #a0a3b1;
            cursor: pointer;
            padding: 5px;
            transition: color 0.2s;
        }

        .chatbot-controls button:hover {
            color: #ffb396;
        }

        .chatbot-body {
            height: 400px;
            display: flex;
            flex-direction: column;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }

        .chat-message {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .chat-message.user {
            flex-direction: row-reverse;
        }

        .chat-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .chat-message.bot .chat-avatar {
            background: #ffb396;
            color: #1a1c23;
        }

        .chat-message.user .chat-avatar {
            background: #4a9eff;
            color: #fff;
        }

        .chat-bubble {
            background: #2a2d35;
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 70%;
        }

        .chat-message.user .chat-bubble {
            background: #4a9eff;
        }

        .chat-text {
            margin: 0;
            color: #fff;
            line-height: 1.4;
        }

        .chat-time {
            font-size: 0.75rem;
            color: #a0a3b1;
            margin-top: 5px;
        }

        .chat-input-area {
            padding: 15px;
            background: #2a2d35;
            border-top: 1px solid #343842;
            display: flex;
            gap: 10px;
        }

        .chat-input {
            flex: 1;
            background: #1a1c23;
            border: 1px solid #343842;
            border-radius: 8px;
            padding: 10px;
            color: #fff;
            font-size: 0.9rem;
            resize: none;
            max-height: 100px;
            transition: all 0.3s ease;
        }

        .chat-input:focus {
            outline: none;
            border-color: #ffb396;
        }

        .chat-send {
            background: #ffb396;
            color: #1a1c23;
            border: none;
            border-radius: 8px;
            width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .chat-send:hover {
            background: #ffa07a;
        }

        /* Scrollbar Styles */
        .chat-messages::-webkit-scrollbar {
            width: 5px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #1a1c23;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #343842;
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #4a4d57;
        }

        @media (max-width: 768px) {
            .chatbot-widget {
                width: calc(100% - 40px);
                bottom: 10px;
                right: 20px;
            }
        }
    </style>
</head>
<body>
    <button class="menu-toggle" onclick="toggleSidebar()">
        <i class="fa-solid fa-bars"></i>
    </button>

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <!-- Kontrol Paneli -->
            <div class="control-panel">
                <div class="control-group">
                    <label for="chart-count"><i class="fas fa-chart-bar"></i> Grafik Sayısı</label>
                    <select id="chart-count" onchange="renderCharts()">
                        <option value="1">1 Grafik</option>
                        <option value="2" selected>2 Grafik</option>
                        <option value="3">3 Grafik</option>
                        <option value="4">4 Grafik</option>
                    </select>
                </div>
                <button class="btn" onclick="refreshData()"><i class="fas fa-sync"></i> Yenile</button>
            </div>

            <!-- İstatistik Kartları -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="title">Toplam Personel</div>
                    <div class="value"><?php echo number_format($stats['total_personel']); ?></div>
                    <div class="change positive">
                        <i class="fas fa-user-plus"></i>
                        <span><?php echo $stats['yeni_personel']; ?> yeni (son 30 gün)</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="title">Aktif Personel</div>
                    <div class="value"><?php echo number_format($stats['aktif_personel']); ?></div>
                    <div class="change <?php echo $stats['verimlilik'] >= 80 ? 'positive' : 'negative'; ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>%<?php echo $stats['verimlilik']; ?> aktif oranı</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="title">Aylık Gider</div>
                    <div class="value">₺<?php echo number_format($stats['aylik_gider']); ?></div>
                    <div class="change">
                        <i class="fas fa-coins"></i>
                        <span>Ort. ₺<?php echo number_format($stats['ortalama_maas']); ?>/kişi</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="title">Departman Sayısı</div>
                    <div class="value"><?php echo count($veriGruplari['departman']['labels']); ?></div>
                    <div class="change positive">
                        <i class="fas fa-sitemap"></i>
                        <span><?php echo array_sum($veriGruplari['departman']['data']); ?> personel</span>
                    </div>
                </div>
            </div>

            <!-- Grafikler -->
            <div class="charts-grid" id="charts-container">
                <!-- Grafikler JavaScript ile eklenecek -->
            </div>
        </div>
    </div>

    <!-- Chatbot Widget -->
    <div class="chatbot-widget" id="chatbot-widget">
        <div class="chatbot-header" onclick="toggleChatbot()">
            <div class="chatbot-title">
                <i class="fas fa-robot"></i>
                <span>MEVARİE Asistan</span>
            </div>
            <div class="chatbot-controls">
                <button class="minimize-btn" id="minimize-btn">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="chatbot-body" id="chatbot-body">
            <div class="chat-messages" id="chat-messages">
                <!-- Mesajlar JavaScript ile eklenecek -->
            </div>
            <div class="chat-input-area">
                <textarea 
                    class="chat-input" 
                    id="chat-input" 
                    placeholder="Mesajınızı yazın..."
                    rows="1"
                ></textarea>
                <button class="chat-send" id="chat-send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

<script>
// PHP'den gelen verileri JavaScript'e aktar
const veriGruplari = <?php echo json_encode($veriGruplari); ?>;
const alanlar = <?php echo json_encode($alanlar); ?>;

// Grafik tiplerini tanımla
const chartTypes = {
    'bar': { label: 'Bar' },
    'line': { label: ' Line Bar' },
    'pie': { label: 'Pie' },
    'doughnut': { label: 'Halka ' },
    'polarArea': { label: 'Polar ' },
    'horizontalBar': { label: 'Horizontal Bar' },
    'radar': { label: 'Radar Grafik' },
    'area': { label: 'Alan Grafik' },
    'stacked': { label: 'Stack Bar' }
};

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Başlangıçta grafikleri oluştur
    renderCharts();
    
    // Her 30 saniyede bir verileri güncelle
    setInterval(refreshData, 30000);
});

// Grafik kartı oluşturma fonksiyonu
function createChartCard(index) {
    const card = document.createElement('div');
    card.className = 'chart-card';
    card.innerHTML = `
        <div class="chart-header">
            <h3>Grafik ${index + 1}</h3>
            <div class="chart-controls">
                <select id="data-select-${index}" onchange="updateChart(${index})">
                    ${Object.entries(alanlar).map(([key, label]) => 
                        `<option value="${key}">${label}</option>`
                    ).join('')}
                </select>
                <select id="type-select-${index}" onchange="updateChart(${index})">
                    ${Object.entries(chartTypes).map(([type, info]) => 
                        `<option value="${type}">${info.label}</option>`
                    ).join('')}
                </select>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="chart-${index}"></canvas>
        </div>
        <div id="chart-summary-${index}" class="chart-summary"></div>
    `;
    return card;
}

// Grafikleri oluştur
function renderCharts() {
    const container = document.getElementById('charts-container');
    container.innerHTML = '';
    const count = parseInt(document.getElementById('chart-count').value);
    
    for (let i = 0; i < count; i++) {
        container.appendChild(createChartCard(i));
        updateChart(i);
    }
}

// Grafik güncelleme fonksiyonu
function updateChart(index) {
    const dataSelect = document.getElementById(`data-select-${index}`);
    const typeSelect = document.getElementById(`type-select-${index}`);
    const canvas = document.getElementById(`chart-${index}`);
    const summaryDiv = document.getElementById(`chart-summary-${index}`);
    
    const selectedData = dataSelect.value;
    const selectedType = typeSelect.value;
    const data = veriGruplari[selectedData];
    
    if (!data) return;
    
    // Eski grafiği temizle
    if (canvas.chart) {
        canvas.chart.destroy();
    }

    // Renk paleti
    const colors = [
        '#ffb396', '#ff9b7b', '#ff8361', '#ff6b46', '#ff532c',
        '#ff3b12', '#f72d00', '#dd2700', '#c32200', '#a91d00'
    ];

    // Grafik konfigürasyonu
    let config = {
        type: selectedType === 'horizontalBar' ? 'bar' : 
              selectedType === 'area' ? 'line' :
              selectedType === 'stacked' ? 'bar' : selectedType,
        data: {
            labels: data.labels,
            datasets: [{
                label: alanlar[selectedData],
                data: data.data,
                backgroundColor: selectedType === 'line' ? colors[0] : colors,
                borderColor: selectedType === 'line' ? colors[0] : colors,
                borderWidth: selectedType === 'line' ? 2 : 1,
                fill: selectedType === 'area',
                tension: ['line', 'area'].includes(selectedType) ? 0.4 : 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: selectedType === 'horizontalBar' ? 'y' : 'x',
            plugins: {
                legend: {
                    display: ['pie', 'doughnut', 'polarArea', 'radar'].includes(selectedType),
                    position: 'bottom',
                    labels: {
                        color: '#fff',
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#1a1c23',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#2a2d35',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: true
                }
            },
            scales: {
                x: {
                    display: !['pie', 'doughnut', 'polarArea'].includes(selectedType),
                    grid: {
                        color: '#2a2d35',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#fff',
                        font: {
                            size: 11
                        }
                    },
                    stacked: selectedType === 'stacked'
                },
                y: {
                    display: !['pie', 'doughnut', 'polarArea'].includes(selectedType),
                    grid: {
                        color: '#2a2d35',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#fff',
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            if (selectedData === 'maas_aralik') {
                                return '₺' + value.toLocaleString();
                            }
                            return value;
                        }
                    },
                    stacked: selectedType === 'stacked'
                }
            }
        }
    };

    // Özel grafik tipleri için ek ayarlar
    if (selectedType === 'radar') {
        config.options.scales = {
            r: {
                angleLines: {
                    color: '#2a2d35'
                },
                grid: {
                    color: '#2a2d35'
                },
                pointLabels: {
                    color: '#fff',
                    font: {
                        size: 11
                    }
                },
                ticks: {
                    color: '#fff',
                    backdropColor: 'transparent'
                }
            }
        };
    }

    if (selectedType === 'stacked') {
        // Veriyi 3 gruba böl
        const total = data.data;
        const group1 = total.map(v => Math.floor(v * 0.4));
        const group2 = total.map(v => Math.floor(v * 0.35));
        const group3 = total.map(v => Math.floor(v * 0.25));

        config.data.datasets = [
            {
                label: 'Grup 1',
                data: group1,
                backgroundColor: colors[0],
                borderColor: 'transparent'
            },
            {
                label: 'Grup 2',
                data: group2,
                backgroundColor: colors[2],
                borderColor: 'transparent'
            },
            {
                label: 'Grup 3',
                data: group3,
                backgroundColor: colors[4],
                borderColor: 'transparent'
            }
        ];
        config.options.plugins.legend.display = true;
    }

    if (selectedType === 'area') {
        config.data.datasets[0].backgroundColor = `${colors[0]}80`;
    }
    
    // Grafiği oluştur
    canvas.chart = new Chart(canvas, config);
    
    // Özet bilgiyi güncelle
    const total = data.data.reduce((a, b) => a + b, 0);
    const max = Math.max(...data.data);
    const maxLabel = data.labels[data.data.indexOf(max)];
    
    summaryDiv.innerHTML = `
        Toplam: ${total.toLocaleString()} | 
        En yüksek: ${maxLabel} (${max.toLocaleString()})
    `;
}

// Verileri yenileme fonksiyonu
function refreshData() {
    fetch('../api/data.php?action=get_stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Grafik verilerini güncelle
                Object.assign(veriGruplari, data.veriGruplari);
                renderCharts();
                
                // İstatistik kartlarını güncelle
                if (data.stats) {
                    updateStatsCards(data.stats);
                }
            }
        })
        .catch(error => console.error('Veri yenileme hatası:', error));
}

// İstatistik kartlarını güncelle
function updateStatsCards(stats) {
    // Toplam personel
    const totalPersonelEl = document.querySelector('.stat-card:nth-child(1) .value');
    if (totalPersonelEl) {
        totalPersonelEl.textContent = stats.total_personel.toLocaleString();
    }
    
    // Yeni personel sayısı
    const yeniPersonelEl = document.querySelector('.stat-card:nth-child(1) .change span');
    if (yeniPersonelEl) {
        yeniPersonelEl.textContent = `${stats.yeni_personel} yeni (son 30 gün)`;
    }
    
    // Aktif personel
    const aktifPersonelEl = document.querySelector('.stat-card:nth-child(2) .value');
    if (aktifPersonelEl) {
        aktifPersonelEl.textContent = stats.aktif_personel.toLocaleString();
    }
    
    // Verimlilik oranı
    const verimlilikEl = document.querySelector('.stat-card:nth-child(2) .change span');
    if (verimlilikEl) {
        verimlilikEl.textContent = `%${stats.verimlilik} aktif oranı`;
    }
    
    // Verimlilik rengini güncelle
    const verimlilikCard = document.querySelector('.stat-card:nth-child(2) .change');
    if (verimlilikCard) {
        verimlilikCard.className = `change ${stats.verimlilik >= 80 ? 'positive' : 'negative'}`;
    }
    
    // Aylık gider
    const aylikGiderEl = document.querySelector('.stat-card:nth-child(3) .value');
    if (aylikGiderEl) {
        aylikGiderEl.textContent = `₺${stats.aylik_gider.toLocaleString()}`;
    }
    
    // Ortalama maaş
    const ortalamaMaasEl = document.querySelector('.stat-card:nth-child(3) .change span');
    if (ortalamaMaasEl) {
        ortalamaMaasEl.textContent = `Ort. ₺${stats.ortalama_maas.toLocaleString()}/kişi`;
    }
    
    // Departman sayısı (veriGruplari'dan al)
    const departmanSayisiEl = document.querySelector('.stat-card:nth-child(4) .value');
    if (departmanSayisiEl && veriGruplari.departman) {
        departmanSayisiEl.textContent = veriGruplari.departman.labels.length;
    }
    
    // Departman personel sayısı
    const departmanPersonelEl = document.querySelector('.stat-card:nth-child(4) .change span');
    if (departmanPersonelEl && veriGruplari.departman) {
        const toplamPersonel = veriGruplari.departman.data.reduce((a, b) => a + b, 0);
        departmanPersonelEl.textContent = `${toplamPersonel} personel`;
    }
}

// Chatbot fonksiyonları
document.addEventListener('DOMContentLoaded', function() {
    const chatbotWidget = document.getElementById('chatbot-widget');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const minimizeBtn = document.getElementById('minimize-btn');

    // Başlangıç mesajını ekle
    addBotMessage("Merhaba! Ben MEVARİE Asistan. Size nasıl yardımcı olabilirim?");

    // Chatbot'u aç/kapat
    function toggleChatbot() {
        chatbotWidget.classList.toggle('minimized');
        if (!chatbotWidget.classList.contains('minimized')) {
            chatInput.focus();
        }
    }

    // Minimize butonu için event listener
    minimizeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleChatbot();
    });

    // Mesaj gönderme fonksiyonu
    function sendMessage() {
        const message = chatInput.value.trim();
        if (message) {
            addUserMessage(message);
            chatInput.value = '';
            // Textarea yüksekliğini sıfırla
            chatInput.style.height = 'auto';
            
            // Bot yanıtını simüle et
            setTimeout(() => {
                processUserMessage(message);
            }, 1000);
        }
    }

    // Kullanıcı mesajını işle
    function processUserMessage(message) {
        // Gemini API'sine istek gönder
        fetch('../api/gemini_chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addBotMessage(data.reply);
            } else {
                addBotMessage("Üzgünüm, bir hata oluştu. Lütfen daha sonra tekrar deneyin.");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            addBotMessage("Üzgünüm, bir hata oluştu. Lütfen daha sonra tekrar deneyin.");
        });
    }

    // Kullanıcı mesajı ekle
    function addUserMessage(message) {
        const messageElement = createMessageElement('user', message);
        chatMessages.appendChild(messageElement);
        scrollToBottom();
    }

    // Bot mesajı ekle
    function addBotMessage(message) {
        const messageElement = createMessageElement('bot', message);
        chatMessages.appendChild(messageElement);
        scrollToBottom();
    }

    // Mesaj elementi oluştur
    function createMessageElement(type, message) {
        const div = document.createElement('div');
        div.className = `chat-message ${type}`;
        
        const avatar = document.createElement('div');
        avatar.className = 'chat-avatar';
        avatar.innerHTML = type === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>';

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';

        const text = document.createElement('p');
        text.className = 'chat-text';
        text.textContent = message;

        const time = document.createElement('div');
        time.className = 'chat-time';
        time.textContent = new Date().toLocaleTimeString();

        bubble.appendChild(text);
        bubble.appendChild(time);
        div.appendChild(avatar);
        div.appendChild(bubble);

        return div;
    }

    // Sohbeti en alta kaydır
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Event listeners
    chatSend.addEventListener('click', sendMessage);

    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Textarea otomatik yükseklik ayarı
    chatInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});
</script>
</body>
</html>
