# 🇬🇧 English

# Software Company ChatGPT-Powered Dashboard Application

## Project Description
This project is a database-driven, chart-supported dashboard application developed to streamline the business processes of a software company. It is designed for use by a single admin and provides information/support through ChatGPT integration.

## Core Features
- **Single Admin Login:** Simple session management (security is not the primary focus).
- **Dashboard:** Visualization of database data through charts and tables, summary information, and quick access buttons.
- **ChatGPT Integration:** A panel where the admin can interact with ChatGPT.
- **Database Management:** All data insertion, updating, and deletion operations are performed exclusively from an admin-only access page.
- **Simple Machine Learning Chart Summaries:** Automated summary extraction based on charts.

## Installation
1. **Database:**
   - Import the `personel.sql` file using phpMyAdmin or a similar tool.
2. **Configuration:**
   - Adjust the database connection settings in the `config/database.php` file according to your environment.
   - If necessary, configure `config/config.php` and set up other API keys (OpenAI, Gemini) in their respective files.
3. **Server:**
   - Run the project files on a PHP server (e.g., XAMPP).

## File Structure
- `api/` : API endpoints (data, ChatGPT, Gemini, personnel operations)
- `config/` : Configuration files (database, API keys)
- `includes/` : Session, authentication, and helper files
- `views/` : Interface files (dashboard, login, database, mail, settings)
- `personel.sql` : Sample database schema

## Usage
- Log in as an admin from the login screen.
- View data, examine summaries, and check chart analyses on the dashboard.
- Interact with ChatGPT via the chatbot panel.
- Manage data from the database page.

## Development Roadmap
1. Database Design and Setup
2. User (Admin) Login System
3. Dashboard Interface and Charts
4. ChatGPT Integration and Interface
5. Data Entry and Management (Admin Only)
6. Simple Machine Learning Chart Summaries
7. Documentation and Final Checks

## License
This project is licensed under the MIT License. See the `LICENSE` file for details.

---

# 🇹🇷 Türkçe

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
