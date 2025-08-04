<?php
// Aktif sayfayı belirle
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="logo">
        <i class="fa-solid fa-chart-line"></i>
        <span>MEVARİE</span>
    </div>
    <nav>
        <a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>
        <a href="database.php" class="<?= $current_page === 'database.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-database"></i>
            <span>Database</span>
        </a>
        <a href="mail.php" class="<?= $current_page === 'mail.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-envelope"></i>
            <span>Mail</span>
        </a>
        <a href="settings.php" class="<?= $current_page === 'settings.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-gear"></i>
            <span>Ayarlar</span>
        </a>
    </nav>
    <div class="user-section">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="user-details">
                <div class="user-name">Kullanıcı</div>
                <div class="user-role">Yönetici</div>
            </div>
        </div>
        <a href="../logout.php" class="logout" title="Çıkış" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?')">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</aside>

<style>
.sidebar {
    width: 260px;
    background: #1a1c23;
    display: flex;
    flex-direction: column;
    padding: 24px 0;
    box-shadow: 2px 0 12px rgba(0,0,0,0.12);
    border-right: 1px solid #2a2d35;
    position: fixed;
    height: 100vh;
    top: 0;
    left: 0;
    z-index: 1000;
}

.sidebar .logo {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 24px;
    margin-bottom: 32px;
}

.sidebar .logo i {
    font-size: 24px;
    color: #ffb396;
}

.sidebar .logo span {
    font-size: 22px;
    font-weight: bold;
    color: #ffb396;
    letter-spacing: 1px;
}

.sidebar nav {
    flex: 1;
    padding: 0 12px;
}

.sidebar nav a {
    display: flex;
    align-items: center;
    color: #a0a3b1;
    text-decoration: none;
    padding: 12px;
    margin-bottom: 4px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.sidebar nav a i {
    width: 24px;
    font-size: 18px;
    margin-right: 12px;
    text-align: center;
}

.sidebar nav a span {
    font-size: 15px;
    font-weight: 500;
}

.sidebar nav a:hover {
    background: #2a2d35;
    color: #fff;
}

.sidebar nav a.active {
    background: #ffb39620;
    color: #ffb396;
}

.user-section {
    padding: 16px 24px;
    border-top: 1px solid #2a2d35;
    margin-top: auto;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: #2a2d35;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-avatar i {
    font-size: 18px;
    color: #ffb396;
}

.user-details {
    flex: 1;
    min-width: 0;
}

.user-name {
    color: #fff;
    font-size: 15px;
    font-weight: 500;
    margin-bottom: 2px;
}

.user-role {
    color: #a0a3b1;
    font-size: 13px;
}

.logout {
    width: 100%;
    padding: 10px;
    border: 1px solid #2a2d35;
    background: transparent;
    color: #ffb396;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.logout:hover {
    background: #2a2d35;
    border-color: #ffb396;
    color: #ffb396;
}

/* Ana içerik alanı için margin */
.main-content {
    margin-left: 260px;
    min-height: 100vh;
    background: #151515;
}

/* Mobil uyumluluk */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .sidebar.active {
        transform: translateX(0);
    }
    
    .main-content {
        margin-left: 0;
    }
}
</style>

<script>
// Mobil menü toggle fonksiyonu
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('active');
}
</script> 