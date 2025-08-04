<?php
// Gerçek backend ve veritabanı işlemleri kaldırıldı
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail Gönder - MEVARİE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { margin: 0; background: #151515; color: #fff; font-family: 'Segoe UI', Arial, sans-serif; }
        .container { max-width: 800px; margin: 40px auto; background: #1a1c23; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.18); padding: 32px 28px; }
        h2 { color: #ffb396; margin-bottom: 18px; }
        .form-mail { margin-bottom: 32px; background: #232323; border-radius: 8px; padding: 18px 14px; }
        .form-row { display: flex; gap: 12px; margin-bottom: 10px; }
        .form-row input, .form-row textarea { background: #2a2d35; color: #fff; border: 1px solid #2a2d35; border-radius: 5px; padding: 7px 10px; font-size: 1rem; }
        .form-row label { color: #ffb396; font-size: 0.98rem; }
        .form-row textarea { min-height: 80px; resize: vertical; width: 100%; }
        .btn-send { background: #ffb396; color: #1a1c23; font-weight: bold; border: none; border-radius: 5px; padding: 10px 28px; font-size: 1.08rem; cursor: pointer; transition: background 0.2s; }
        .btn-send:hover { background: #ffa07a; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; background: #232323; border-radius: 8px; overflow: hidden; }
        th, td { padding: 10px 8px; text-align: left; }
        th { background: #2a2d35; color: #ffb396; font-weight: 600; }
        tr:nth-child(even) { background: #1f2128; }
        tr:hover { background: #2a2d35; }
        td { color: #a0a3b1; }
        @media (max-width: 900px) { 
            .form-row { flex-direction: column; }
            .container { margin: 20px; }
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
            .menu-toggle {
                display: block;
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
            <h2>Mail Gönder (Demo)</h2>
            <form class="form-mail" id="mail-form" autocomplete="off" onsubmit="fakeSend(event)">
                <div class="form-row">
                    <label>Alıcı <input type="email" name="alici" required></label>
                    <label>Konu <input type="text" name="konu" required></label>
                </div>
                <div class="form-row">
                    <label style="flex:1;">Mesaj <textarea name="mesaj" required></textarea></label>
                </div>
                <button class="btn-send" type="submit"><i class="fa fa-paper-plane"></i> Gönder</button>
                <span id="mail-result" style="margin-left:18px;color:#ffb396;font-weight:bold;"></span>
            </form>
            <h2>Gönderilen Mailler (Demo)</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Alıcı</th>
                        <th>Konu</th>
                        <th>Mesaj</th>
                        <th>Tarih</th>
                    </tr>
                </thead>
                <tbody id="mail-list">
                    <tr>
                        <td>1</td>
                        <td>ornek@eposta.com</td>
                        <td>Hoşgeldiniz</td>
                        <td>Merhaba, sistemimize hoşgeldiniz!</td>
                        <td>2024-05-01 12:00</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>test@demo.com</td>
                        <td>Test Maili</td>
                        <td>Bu bir test mailidir.</td>
                        <td>2024-05-02 09:30</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<script>
function fakeSend(e) {
    e.preventDefault();
    const form = e.target;
    const alici = form.alici.value;
    const konu = form.konu.value;
    const mesaj = form.mesaj.value;
    document.getElementById('mail-result').innerText = 'Gönderildi (Demo)!';
    // Demo olarak tabloya ekle
    const tbody = document.getElementById('mail-list');
    const row = document.createElement('tr');
    row.innerHTML = `<td>Y</td><td>${alici}</td><td>${konu}</td><td>${mesaj}</td><td>${new Date().toLocaleString('tr-TR')}</td>`;
    tbody.prepend(row);
    form.reset();
}
</script>
</body>
</html> 