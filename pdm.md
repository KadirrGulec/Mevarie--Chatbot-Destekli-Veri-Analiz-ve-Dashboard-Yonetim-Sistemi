# Yazılım Şirketi ChatGPT Destekli Dashboard Uygulaması Planı

## 1. Amaç
- Yazılım şirketinin iş süreçlerini kolaylaştırmak.
- Tek bir adminin kullanacağı, veritabanı tabanlı, grafiklerle desteklenen bir dashboard sunmak.
- ChatGPT entegrasyonu ile bilgi/destek sağlamak.

## 2. Temel Özellikler

### 2.1. Kullanıcı Yönetimi
- **Tek Admin Girişi:** Basit oturum yönetimi (güvenlik öncelikli değil).
- **Giriş Ekranı:** (views/login.php)

### 2.2. Dashboard
- **Veri Görselleştirme:** Grafikler ve tablolar ile veritabanındaki verilerin gösterimi.
- **Basit Makine Öğrenmesi ile Özet:** Grafikler üzerinde otomatik özet çıkarımı (ör: "Bu bir pie grafiği ve cinsiyetlere göre ayrılmıştır, erkek %57 daha fazla").
- **Özet Bilgiler:** Toplam proje, müşteri, gelir vb. istatistikler.
- **Hızlı Erişim:** Sık kullanılan işlemlere hızlı erişim butonları.
- (views/dashboard.php)

### 2.3. ChatGPT Entegrasyonu
- **Chatbot Arayüzü:** Adminin ChatGPT ile etkileşime geçebileceği bir panel.
- **Bilgi ve Destek:** ChatGPT üzerinden sadece bilgi alma ve destek amaçlı kullanım.
- (views/chatbot.php, api/chat.php)
- **Not:** Chatbot motoru yazılmayacak, doğrudan ChatGPT API kullanılacak.

### 2.4. Veritabanı Yönetimi
- **Sadece Admin Erişimi:** Tüm veri ekleme, güncelleme ve silme işlemleri sadece adminin erişebildiği "database" sayfasından yapılacak.
- **Veri Listeleme:** Tüm verilerin tablo halinde gösterimi.
- (views/database.php, api/data.php)

## 3. Teknik Yapı

### 3.1. Backend
- **PHP tabanlı API:** (api/data.php, api/chat.php)
- **Veritabanı:** MySQL (config/database.php)
- **Oturum ve Kimlik Doğrulama:** (includes/auth.php, includes/session.php) (Basit düzeyde)

### 3.2. Frontend
- **PHP ile dinamik sayfalar:** (views/)
- **Dashboard ve Chatbot arayüzleri**
- **Grafik kütüphanesi entegrasyonu:** (örn. Chart.js veya Google Charts)
- **Makine öğrenmesi özetleri için basit PHP/JS fonksiyonları**

### 3.3. Yapılandırma
- **config/config.php:** Genel ayarlar ve yardımcı fonksiyonlar
- **config/database.php:** Veritabanı bağlantı ayarları

## 4. Geliştirme Aşamaları

1. **Veritabanı Tasarımı ve Kurulumu**
2. **Kullanıcı (Admin) Giriş Sistemi**
3. **Dashboard Arayüzü ve Grafikler**
4. **ChatGPT Entegrasyonu ve Arayüzü**
5. **Veri Girişi ve Yönetimi (Sadece Admin)**
6. **Basit Makine Öğrenmesi ile Grafik Özetleri**
7. **Dokümantasyon ve Son Kontroller**
