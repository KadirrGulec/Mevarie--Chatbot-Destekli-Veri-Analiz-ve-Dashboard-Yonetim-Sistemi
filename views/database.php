<?php
require_once '../config/database.php';

// Personel listesini çek
$sql = "SELECT * FROM personel ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$personel_listesi = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $personel_listesi[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database - MEVARİE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { 
            margin: 0; 
            background: #151515; 
            color: #fff; 
            font-family: 'Segoe UI', Arial, sans-serif; 
        }
        
        .container { 
            padding: 40px; 
        }
        
        /* Personel Yönetimi Formu */
        .personel-form {
            background: #1a1c23;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 32px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            color: #ffb396;
            font-size: 0.9rem;
        }
        
        .form-group input,
        .form-group select {
            background: #2a2d35;
            border: 1px solid #343842;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ffb396;
        }
        
        .btn {
            background: #ffb396;
            color: #1a1c23;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }
        
        .btn:hover {
            background: #ffa07a;
        }
        
        /* Personel Tablosu */
        .personel-table {
            width: 100%;
            border-collapse: collapse;
            background: #1a1c23;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .personel-table th,
        .personel-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #2a2d35;
        }
        
        .personel-table th {
            background: #2a2d35;
            color: #ffb396;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .personel-table tr:hover {
            background: #2a2d35;
        }
        
        .personel-table .actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .btn-edit {
            background: #3498db;
            color: #fff;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: #fff;
        }
        
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .page-header h2 {
            color: #ffb396;
            margin: 0;
        }
        
        .search-box {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        
        .search-box input {
            background: #2a2d35;
            border: 1px solid #343842;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            min-width: 250px;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #ffb396;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .page-header {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
            
            .search-box {
                width: 100%;
            }
            
            .search-box input {
                flex: 1;
            }
        }
        
        /* Toast bildirimi stilleri */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            border-radius: 6px;
            color: #fff;
            font-size: 0.9rem;
            z-index: 1000;
            transform: translateX(120%);
            transition: transform 0.3s ease-in-out;
        }
        
        .toast.show {
            transform: translateX(0);
        }
        
        .toast-success {
            background: #2ecc71;
        }
        
        .toast-error {
            background: #e74c3c;
        }
        
        /* Spinner animasyonu */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .fa-spin {
            animation: spin 1s linear infinite;
        }
        
        /* Form validasyonu */
        .form-group input:invalid,
        .form-group select:invalid {
            border-color: #e74c3c;
        }
        
        .form-group input:valid,
        .form-group select:valid {
            border-color: #2ecc71;
        }

        /* Responsive table columns */
        @media screen and (max-width: 1200px) {
            .personel-table th:nth-child(4),
            .personel-table td:nth-child(4),
            .personel-table th:nth-child(5),
            .personel-table td:nth-child(5),
            .personel-table th:nth-child(7),
            .personel-table td:nth-child(7),
            .personel-table th:nth-child(8),
            .personel-table td:nth-child(8),
            .personel-table th:nth-child(9),
            .personel-table td:nth-child(9),
            .personel-table th:nth-child(10),
            .personel-table td:nth-child(10) {
                display: none;
            }
        }

        @media screen and (max-width: 768px) {
            .personel-table th:nth-child(11),
            .personel-table td:nth-child(11),
            .personel-table th:nth-child(12),
            .personel-table td:nth-child(12) {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <h2>Personel Yönetimi</h2>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Personel ara...">
                    <button class="btn" onclick="toggleForm()">
                        <i class="fas fa-plus"></i> Yeni Personel
                    </button>
                </div>
            </div>

            <!-- Personel Ekleme/Düzenleme Formu -->
            <div class="personel-form" id="personelForm" style="display: none;">
                <form id="personelFormElement" method="post" action="../api/personel.php">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Ad</label>
                            <input type="text" name="ad" required>
                        </div>
                        <div class="form-group">
                            <label>Soyad</label>
                            <input type="text" name="soyad" required>
                        </div>
                        <div class="form-group">
                            <label>Cinsiyet</label>
                            <select name="cinsiyet" required>
                                <option value="">Seçiniz</option>
                                <option value="Erkek">Erkek</option>
                                <option value="Kadın">Kadın</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Yaş</label>
                            <input type="number" name="yas" required>
                        </div>
                        <div class="form-group">
                            <label>Pozisyon</label>
                            <input type="text" name="pozisyon" required>
                        </div>
                        <div class="form-group">
                            <label>Departman</label>
                            <input type="text" name="departman" required>
                        </div>
                        <div class="form-group">
                            <label>Maaş</label>
                            <input type="number" name="maas" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Giriş Tarihi</label>
                            <input type="date" name="giris_tarihi" required>
                        </div>
                        <div class="form-group">
                            <label>Şehir</label>
                            <input type="text" name="sehir" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Durum</label>
                            <select name="durum" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Pasif">Pasif</option>
                                <option value="İzinde">İzinde</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <button type="button" class="btn" style="background: #2a2d35; color: #ffb396;" onclick="toggleForm()">
                            <i class="fas fa-times"></i> İptal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Personel Tablosu -->
            <div class="table-container">
                <table class="personel-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ad</th>
                            <th>Soyad</th>
                            <th>Cinsiyet</th>
                            <th>Yaş</th>
                            <th>Pozisyon</th>
                            <th>Departman</th>
                            <th>Maaş</th>
                            <th>Giriş Tarihi</th>
                            <th>Şehir</th>
                            <th>Email</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="personelTableBody">
                        <?php foreach ($personel_listesi as $personel): ?>
                        <tr>
                            <td><?php echo $personel['id']; ?></td>
                            <td><?php echo htmlspecialchars($personel['ad']); ?></td>
                            <td><?php echo htmlspecialchars($personel['soyad']); ?></td>
                            <td><?php echo htmlspecialchars($personel['cinsiyet']); ?></td>
                            <td><?php echo $personel['yas']; ?></td>
                            <td><?php echo htmlspecialchars($personel['pozisyon']); ?></td>
                            <td><?php echo htmlspecialchars($personel['departman']); ?></td>
                            <td><?php echo number_format($personel['maas'], 2); ?></td>
                            <td><?php echo $personel['giris_tarihi']; ?></td>
                            <td><?php echo htmlspecialchars($personel['sehir']); ?></td>
                            <td><?php echo htmlspecialchars($personel['email']); ?></td>
                            <td><?php echo htmlspecialchars($personel['durum']); ?></td>
                            <td class="actions">
                                <button class="btn-action btn-edit" onclick="editPersonel(<?php echo $personel['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action btn-delete" onclick="deletePersonel(<?php echo $personel['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    // Toast bildirimi göster
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Form göster/gizle
    function toggleForm() {
        const form = document.getElementById('personelForm');
        const formElement = document.getElementById('personelFormElement');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
        formElement.reset();
        formElement.action = '../api/personel.php?action=add';
        formElement.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> Kaydet';
    }

    // Personel düzenle
    function editPersonel(id) {
        fetch(`../api/personel.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const form = document.getElementById('personelFormElement');
                    form.style.display = 'block';
                    document.getElementById('personelForm').style.display = 'block';
                    
                    for (let key in data.personel) {
                        const input = form.elements[key];
                        if (input) {
                            input.value = data.personel[key];
                        }
                    }
                    
                    form.action = `../api/personel.php?action=update&id=${id}`;
                    form.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> Güncelle';
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                showToast('Personel bilgileri alınırken hata oluştu', 'error');
            });
    }

    // Personel sil
    function deletePersonel(id) {
        if (confirm('Bu personeli silmek istediğinizden emin misiniz?')) {
            fetch(`../api/personel.php?action=delete&id=${id}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Personel başarıyla silindi');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                showToast('Silme işlemi sırasında hata oluştu', 'error');
            });
        }
    }

    // Form submit
    document.getElementById('personelFormElement').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Submit butonunu devre dışı bırak
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> İşleniyor...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            showToast('İşlem sırasında hata oluştu', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Arama fonksiyonu
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.getElementById('personelTableBody').getElementsByTagName('tr');
        let hasResults = false;
        
        for (let row of rows) {
            const text = row.textContent.toLowerCase();
            const visible = text.includes(searchText);
            row.style.display = visible ? '' : 'none';
            if (visible) hasResults = true;
        }
        
        // Sonuç bulunamadı mesajı
        let noResults = document.getElementById('noResults');
        if (!hasResults) {
            if (!noResults) {
                noResults = document.createElement('tr');
                noResults.id = 'noResults';
                noResults.innerHTML = `<td colspan="13" style="text-align: center; padding: 20px;">Sonuç bulunamadı</td>`;
                document.getElementById('personelTableBody').appendChild(noResults);
            }
        } else if (noResults) {
            noResults.remove();
        }
    });
    </script>
</body>
</html>
