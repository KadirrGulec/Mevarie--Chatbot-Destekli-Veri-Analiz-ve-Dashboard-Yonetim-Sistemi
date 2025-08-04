<?php // Demo ayarlar sayfası, backend yok ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - MEVARİE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { margin: 0; background: #151515; color: #fff; font-family: 'Segoe UI', Arial, sans-serif; }
        .container { max-width: 600px; margin: 40px auto; background: #1a1c23; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.18); padding: 32px 28px; }
        h2 { color: #ffb396; margin-bottom: 18px; }
        .settings-form { display: flex; flex-direction: column; gap: 22px; }
        .settings-row { display: flex; align-items: center; gap: 18px; }
        .settings-row label { color: #ffb396; min-width: 120px; font-size: 1.05rem; }
        .settings-row input[type="email"], .settings-row input[type="password"] { background: #232323; color: #fff; border: 1px solid #2a2d35; border-radius: 5px; padding: 7px 10px; font-size: 1rem; }
        .settings-row select { background: #232323; color: #fff; border: 1px solid #2a2d35; border-radius: 5px; padding: 7px 10px; font-size: 1rem; }
        .btn-save { background: #ffb396; color: #1a1c23; font-weight: bold; border: none; border-radius: 5px; padding: 10px 28px; font-size: 1.08rem; cursor: pointer; transition: background 0.2s; margin-top: 18px; }
        .btn-save:hover { background: #ffa07a; }
        .settings-info { color: #a0a3b1; font-size: 0.98rem; margin-top: 8px; }
        @media (max-width: 900px) { 
            .settings-row { flex-direction: column; align-items: flex-start; }
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
            <h2>Ayarlar</h2>
            <form class="settings-form" onsubmit="saveSettings(event)">
                <div class="settings-row">
                    <label>Kullanıcı E-posta</label>
                    <input type="email" value="mevarie@mail.com" disabled>
                </div>
                <div class="settings-row">
                    <label>Şifre Değiştir</label>
                    <input type="password" placeholder="Yeni şifre (demo)">
                </div>
                <div class="settings-row">
                    <label>Tema</label>
                    <select id="theme-select" onchange="changeTheme()">
                        <option value="dark" selected>Koyu</option>
                        <option value="light">Açık</option>
                    </select>
                </div>
                <button class="btn-save" type="submit"><i class="fa fa-save"></i> Kaydet</button>
                <div class="settings-info" id="settings-info"></div>
            </form>
        </div>
    </div>

<script>
function saveSettings(e) {
    e.preventDefault();
    document.getElementById('settings-info').innerText = 'Ayarlar kaydedildi (Demo)';
    setTimeout(()=>{
        document.getElementById('settings-info').innerText = '';
    }, 2000);
}

function changeTheme() {
    const theme = document.getElementById('theme-select').value;
    if (theme === 'light') {
        document.body.style.background = '#f4f4f4';
        document.body.style.color = '#222';
        document.querySelectorAll('.container').forEach(el=>{
            el.style.background = '#fff';
            el.style.color = '#222';
        });
    } else {
        document.body.style.background = '#151515';
        document.body.style.color = '#fff';
        document.querySelectorAll('.container').forEach(el=>{
            el.style.background = '#1a1c23';
            el.style.color = '#fff';
        });
    }
}
</script>
</body>
</html> 