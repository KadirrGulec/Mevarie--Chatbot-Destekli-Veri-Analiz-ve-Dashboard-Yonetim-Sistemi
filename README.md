# Yazılım Şirketi ChatGPT Destekli Dashboard Uygulaması

## Proje Tanımı
Bu proje, bir yazılım şirketinin iş süreçlerini kolaylaştırmak amacıyla geliştirilmiş, tek adminin kullanacağı, veritabanı tabanlı ve grafiklerle desteklenen bir dashboard uygulamasıdır. ChatGPT entegrasyonu ile bilgi/destek sağlanır.

## Temel Özellikler
- **Tek Admin Girişi:** Basit oturum yönetimi (güvenlik öncelikli değildir).
- **Dashboard:** Grafikler ve tablolar ile veritabanındaki verilerin görselleştirilmesi, özet bilgiler, hızlı erişim butonları.
- **ChatGPT Entegrasyonu:** Adminin ChatGPT ile etkileşime geçebileceği bir panel.
- **Veritabanı Yönetimi:** Tüm veri ekleme, güncelleme ve silme işlemleri sadece adminin erişebildiği sayfadan yapılır.
- **Basit Makine Öğrenmesi ile Grafik Özetleri:** Grafikler üzerinde otomatik özet çıkarımı.

## Kurulum
1. **Veritabanı:**
   - `personel.sql` dosyasını phpMyAdmin veya benzeri bir araçla içe aktarın.
2. **Yapılandırma:**
   - `config/database.php` dosyasındaki veritabanı bağlantı ayarlarını kendi ortamınıza göre düzenleyin.
   - Gerekirse `config/config.php` ve diğer API anahtarlarını (OpenAI, Gemini) ilgili dosyalardan ayarlayın.
3. **Sunucu:**
   - Proje dosyalarını bir PHP sunucusunda (örn. XAMPP) çalıştırın.

## Dosya Yapısı
- `api/` : API uç noktaları (veri, ChatGPT, Gemini, personel işlemleri)
- `config/` : Yapılandırma dosyaları (veritabanı, API anahtarları)
- `includes/` : Oturum, kimlik doğrulama ve yardımcı dosyalar
- `views/` : Arayüz dosyaları (dashboard, login, database, mail, settings)
- `personel.sql` : Örnek veritabanı şeması

## Kullanım
- Giriş ekranından admin olarak giriş yapın.
- Dashboard üzerinden verileri görüntüleyin, özetleri ve grafik analizlerini inceleyin.
- Chatbot panelinden ChatGPT ile etkileşime geçin.
- Database sayfasından verileri yönetin.

## Geliştirme Yol Haritası
1. Veritabanı Tasarımı ve Kurulumu
2. Kullanıcı (Admin) Giriş Sistemi
3. Dashboard Arayüzü ve Grafikler
4. ChatGPT Entegrasyonu ve Arayüzü
5. Veri Girişi ve Yönetimi (Sadece Admin)
6. Basit Makine Öğrenmesi ile Grafik Özetleri
7. Dokümantasyon ve Son Kontroller

## Lisans
Bu proje MIT lisansı ile lisanslanmıştır. Ayrıntılar için `LICENSE` dosyasına bakınız. 