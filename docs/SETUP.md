# DanglingWeb — Setup Guide

Panduan lengkap setup project setelah clone repository.

---

## Prasyarat

| Software | Versi Minimum |
|----------|---------------|
| PHP | 8.4+ |
| Composer | 2.x |
| MySQL / MariaDB | 5.7+ / 10.3+ |
| Node.js + NPM | 18+ (untuk asset frontend) |
| Git | 2.x |

---

## 1. Clone & Install Dependencies

```bash
git clone <repository-url> DanglingWeb
cd DanglingWeb

# Install PHP dependencies
composer install

# Install frontend dependencies (jika ada asset Vite/Mix)
npm install
```

---

## 2. Konfigurasi Environment

```bash
# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit file `.env` dan sesuaikan:

```env
# === Database ===
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dangling_web
DB_USERNAME=root
DB_PASSWORD=your_password

# === App ===
APP_NAME=DanglingWeb
APP_URL=http://localhost:8000

# === Order Config ===
# Batas waktu (menit) buyer boleh cancel order. Kosongkan = tanpa batas.
ORDER_BUYER_CANCEL_TIMEOUT_MINUTES=10

# === Firebase Cloud Messaging (Opsional) ===
# Ambil dari Firebase Console > Project Settings > Cloud Messaging > Server Key
FCM_SERVER_KEY=
```

---

## 3. Database

```bash
# Buat database MySQL terlebih dahulu
# mysql -u root -p -e "CREATE DATABASE dangling_web;"

# Jalankan migration
php artisan migrate

# Jalankan seeder (role + permission + default settings)
php artisan db:seed
php artisan db:seed --class=SettingsSeeder
```

### Daftar Migration

| Migration | Keterangan |
|-----------|------------|
| Default Laravel | users, password_resets, dll. |
| Passport | oauth_clients, oauth_tokens, dll. |
| Spatie Permission | roles, permissions, model_has_roles |
| App: orders, order_items | Tabel order utama |
| App: sellers, buyers, products | Tabel bisnis utama |
| App: complaints | Tabel keluhan + rating |
| `000001` | sellers: +is_online, rating_average, rating_count, open_time, close_time |
| `000002` | complaints: +order_id, status, handled_by, handled_at |
| `000003` | buyer_favorites (tabel baru) |
| `000004` | orders: +payment_status |
| `000005` | products: +is_active |
| `000006` | settings (tabel baru) |
| `000007` | activity_logs (tabel baru) |
| `000008` | device_tokens (tabel baru) |
| `000009` | conversations (tabel baru fitur chat) |
| `000010` | messages (tabel baru fitur chat) |
| `000011` | reviews (tabel baru pemisah rating) |
| `000012` | vouchers (tabel baru kode promo) |

---

## 4. Laravel Passport (Autentikasi API)

```bash
# Install Passport keys
php artisan passport:install

# Atau jika sudah pernah install, cukup:
php artisan passport:keys
```

Pastikan output `personal access client` tersimpan. Client ID dan Secret digunakan oleh mobile app.

---

## 5. Storage Link

```bash
# Buat symlink public/storage → storage/app/public
php artisan storage:link
```

Diperlukan agar foto produk dan profil bisa diakses via URL.

---

## 6. Jalankan Server Development

```bash
# Laravel development server
php artisan serve

# Frontend assets (terminal terpisah, jika pakai Vite)
npm run dev
```

Akses:
- **Web Panel Admin**: http://localhost:8000/login
- **API Base URL**: http://localhost:8000/api
- **API Documentation**: http://localhost:8000/api/documentation

---

## 7. API Documentation (Swagger)

Aplikasi API DanglingWeb telah dilengkapi dengan dokumentasi OpenAPI / Swagger.

1. Publish configuration swagger (jika belum ada):
```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

2. Generate file `swagger.json` terbaru jika ada perubahan coding API:
```bash
$env:COMPOSER_PROCESS_TIMEOUT=2000; php artisan l5-swagger:generate
```
*Catatan: Jika memakai linux/macOS cukup gunakan `COMPOSER_PROCESS_TIMEOUT=2000 php artisan l5-swagger:generate`*

Akses dokumentasi UI lewat browser di URL `/api/documentation`.

## 8. Scheduler & Queue (Production)

### Scheduler (Wajib di Production)

Tambahkan **satu baris** cron di server:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduler menjalankan:
- `orders:auto-cancel` — setiap 5 menit (cancel order pending yang timeout)
- `orders:auto-complete` — setiap 15 menit (complete order accepted yang timeout)

**Test manual:**

```bash
php artisan orders:auto-cancel
php artisan orders:auto-complete
```

### Queue (Opsional)

Default menggunakan `sync` (langsung diproses). Untuk production, ubah ke database/redis:

```env
QUEUE_CONNECTION=database
```

Lalu jalankan worker:

```bash
php artisan queue:work
```

---

## 9. Firebase Cloud Messaging (Opsional)

Diperlukan untuk push notification ke mobile app (buyer & seller).

1. Buka [Firebase Console](https://console.firebase.google.com/)
2. Buat/pilih project → **Project Settings** → **Cloud Messaging**
3. Salin **Server Key**
4. Tambahkan di `.env`:

```env
FCM_SERVER_KEY=your_server_key_here
```

Tanpa konfigurasi ini, push notification akan di-skip (tidak error).

---

## 10. Konfigurasi Sistem (Admin Panel)

Setelah setup, buka http://localhost:8000/admin/settings untuk mengatur:

| Setting | Deskripsi |
|---------|-----------|
| `order.buyer_cancel_timeout_minutes` | Batas waktu cancel buyer (menit) |
| `order.auto_complete_hours` | Auto-complete order accepted (jam) |
| `seller.default_radius_km` | Radius pencarian default (km) |
| `seller.max_products` | Maks produk per seller |
| `complaint.allow_anonymous` | Izinkan keluhan anonim |
| `app.maintenance_mode` | Mode maintenance |
| `app.announcement` | Teks pengumuman |

---

## Ringkasan Perintah Setup (Quick Start)

```bash
# 1. Clone & install
git clone <repo-url> DanglingWeb && cd DanglingWeb
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate
# Edit .env → sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 3. Database
php artisan migrate
php artisan db:seed
php artisan db:seed --class=SettingsSeeder

# 4. Passport
php artisan passport:install

# 5. Storage
php artisan storage:link

# 6. Run
php artisan serve
```

---

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| `SQLSTATE[42S01] Table already exists` | Jalankan `php artisan migrate:fresh` (⚠️ hapus semua data) |
| `Personal access client not found` | Jalankan `php artisan passport:install` |
| Foto produk tidak tampil | Jalankan `php artisan storage:link` |
| Push notification tidak terkirim | Cek `FCM_SERVER_KEY` di `.env` dan log di `storage/logs/laravel.log` |
| Scheduler tidak jalan | Pastikan cron entry sudah ditambahkan di server |
