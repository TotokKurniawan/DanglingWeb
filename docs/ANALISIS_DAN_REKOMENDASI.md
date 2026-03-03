# Analisis & Rekomendasi — Peningkatan & Fitur Baru

Terakhir diperbarui: **3 Maret 2026**

Hasil review mendalam terhadap seluruh codebase DanglingWeb: 12 model, 15 service, 26 controller, routes API & web.

---

## A. Peningkatan Fitur yang Sudah Ada

### 1. Keamanan & Autentikasi

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 1.1 | **Password reset via API** | 🔴 Tinggi | Saat ini hanya ada `forgot-password` di web. Buyer/seller mobile tidak bisa reset password. Butuh endpoint `POST /api/forgot-password` + `POST /api/reset-password` dengan OTP via email/SMS. |
| 1.2 | **Email verification** | 🟡 Sedang | Registrasi langsung aktif tanpa verifikasi email. Tambahkan flow email verification setelah register. |
| 1.3 | **Rate limiting per-user** | 🟡 Sedang | Throttle saat ini global (`throttle:10,1`). Tingkatkan ke per-user rate limiting untuk mencegah abuse. |
| 1.4 | **Refresh token** | 🟡 Sedang | Passport token saat ini tidak ada mekanisme refresh. Jika token expired, user harus login ulang. Tambahkan `POST /api/token/refresh`. |
| 1.5 | **Change password API** | 🔴 Tinggi | Tidak ada endpoint untuk ganti password. Butuh `PUT /api/change-password` (old_password + new_password). |

---

### 2. Manajemen Produk

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 2.1 | **Kategori produk (master table)** | 🔴 Tinggi | Saat ini `category` di Product hanya string bebas. Buat tabel `categories` agar konsisten, bisa difilter, dan tidak typo. |
| 2.2 | **Deskripsi produk** | 🟡 Sedang | Product hanya punya `name`, `price`, `category`, `photo_path`. Tambahkan field `description` untuk detail produk. |
| 2.3 | **Multiple foto produk** | 🟡 Sedang | Saat ini hanya 1 foto per produk (`photo_path`). Buat tabel `product_images` untuk galeri foto. |
| 2.4 | **Stok produk** | 🔴 Tinggi | Tidak ada konsep stok (jumlah barang). Tambahkan field `stock` dan validasi saat order agar tidak melebihi stok. Auto-deduct saat order accepted. |
| 2.5 | **Pencarian produk** | 🟡 Sedang | Belum ada endpoint pencarian produk lintas seller. Butuh `GET /api/products/search?q=...&category=...`. |

---

### 3. Order & Pembayaran

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 3.1 | **Detail order endpoint** | 🔴 Tinggi | Tidak ada `GET /api/orders/{id}` untuk melihat detail satu order. Buyer/seller harus pull seluruh history. |
| 3.2 | **Konfirmasi pembayaran** | 🔴 Tinggi | Field `payment_status` ada tapi tidak ada endpoint untuk update (misal upload bukti transfer). Butuh `PUT /api/orders/{id}/confirm-payment`. |
| 3.3 | **Bukti pembayaran (foto)** | 🟡 Sedang | Untuk metode TRANSFER, buyer perlu upload foto bukti. Tambahkan field `payment_proof_path` di orders. |
| 3.4 | **Catatan order** | 🟢 Rendah | Buyer tidak bisa menambahkan catatan ke order (misal "jangan pakai sambal"). Tambahkan field `notes` di orders. |
| 3.5 | **Total harga di response** | 🟡 Sedang | Response order tidak menampilkan total harga keseluruhan. Tambahkan accessor `total_price` (sum `unit_price * quantity`). |

---

### 4. Profil & Akun

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 4.1 | **Upload foto profil via API** | 🟡 Sedang | Saat ini `photo_path` ada tapi tidak ada endpoint upload foto terpisah. Butuh `POST /api/profile/photo`. |
| 4.2 | **Get profil sendiri** | 🔴 Tinggi | `GET /api/user` hanya return data User. Tidak termasuk data Buyer/Seller. Tambahkan relasi di response. |
| 4.3 | **Delete account** | 🟢 Rendah | Tidak ada fitur hapus akun. Untuk kepatuhan privasi, tambahkan `DELETE /api/account` (soft delete). |

---

### 5. Admin Panel Web

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 5.1 | **Manajemen user (buyer list)** | 🟡 Sedang | Admin bisa lihat seller dan operator, tapi tidak ada halaman daftar buyer. Tambahkan `/admin/buyers`. |
| 5.2 | **Detail order di admin** | 🟡 Sedang | Admin tidak bisa melihat detail order individual. Tambahkan `/admin/orders` + `/admin/orders/{id}`. |
| 5.3 | **Export data (CSV/Excel)** | 🟡 Sedang | Dashboard menampilkan statistik tapi tidak bisa di-export. Tambahkan tombol export untuk orders, sellers, complaints. |
| 5.4 | **Activity log viewer** | 🟡 Sedang | Tabel `activity_logs` sudah ada tapi belum ada halaman admin untuk melihatnya. Tambahkan `/admin/activity-logs`. |
| 5.5 | **Suspend/unsuspend seller** | 🔴 Tinggi | Admin bisa lihat seller tapi tidak bisa suspend akun bermasalah. Tambahkan aksi suspend + field `is_suspended` di sellers. |

---

## B. Fitur Baru yang Disarankan

### 6. Chat / Messaging

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 6.1 | **Chat buyer ↔ seller** | 🟡 Sedang | Buyer tidak bisa komunikasi dengan seller sebelum/sesudah order. Buat tabel `conversations` + `messages` dan endpoint API. |
| 6.2 | **Notifikasi pesan baru** | 🟡 Sedang | Integrasi dengan FCM yang sudah ada untuk notify pesan baru. |

---

### 7. Promo & Diskon

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 7.1 | **Voucher / kode promo** | 🟡 Sedang | Buat tabel `vouchers` (code, discount_type, discount_value, valid_until, max_uses). Endpoint `POST /api/vouchers/validate`. |
| 7.2 | **Harga coret (diskon produk)** | 🟢 Rendah | Tambahkan field `original_price` di products agar seller bisa tampilkan harga sebelum diskon. |

---

### 8. Notifikasi In-App

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 8.1 | **Tabel notifikasi** | 🔴 Tinggi | Push notification sudah ada tapi tidak ada riwayat notifikasi di app. Buat tabel `notifications` (user_id, title, body, is_read, data) agar bisa ditampilkan sebagai inbox. |
| 8.2 | **Endpoint notifikasi** | 🔴 Tinggi | `GET /api/notifications`, `PUT /api/notifications/{id}/read`, `GET /api/notifications/unread-count`. |

---

### 9. Review & Rating Terpisah dari Complaint

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 9.1 | **Pisahkan review dari complaint** | 🟡 Sedang | Saat ini rating digabung di complaint. Idealnya review (bintang + komentar positif) terpisah dari complaint (keluhan). Buat tabel `reviews` khusus. |
| 9.2 | **Reply dari seller** | 🟢 Rendah | Seller bisa membalas review buyer. Tambahkan `seller_reply` + `replied_at` di reviews. |

---

### 10. Laporan & Analitik

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 10.1 | **Laporan pendapatan seller (export)** | 🟡 Sedang | Seller bisa lihat stats tapi tidak bisa download laporan. Tambahkan `GET /api/sellers/me/report?period=monthly` yang return PDF/CSV. |
| 10.2 | **Analitik produk terlaris** | 🟢 Rendah | Dashboard seller menampilkan total order tapi tidak per-produk. Tambahkan "produk terlaris" berdasarkan jumlah order. |

---

### 11. Lokasi & Tracking

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 11.1 | **Update lokasi seller real-time** | 🟡 Sedang | Seller (pedagang keliling) berpindah lokasi. Saat ini lat/lng di-set sekali. Tambahkan `PUT /api/sellers/me/location` yang di-call periodik dari mobile. |
| 11.2 | **Riwayat lokasi seller** | 🟢 Rendah | Simpan riwayat lokasi agar buyer bisa prediksi rute pedagang. Tabel `seller_location_logs`. |
| 11.3 | **ETA (Estimated Time Arrival)** | 🟢 Rendah | Hitung estimasi kedatangan seller berdasarkan jarak + kecepatan rata-rata. |

---

### 12. Testing & Code Quality

| # | Item | Prioritas | Keterangan |
|---|------|-----------|------------|
| 12.1 | **Unit test model & service** | 🔴 Tinggi | Belum ada test suite. Prioritaskan test untuk OrderService, ComplaintService, dan SellerRatingService. |
| 12.2 | **Feature test API endpoints** | 🔴 Tinggi | Test setiap endpoint API: register, login, create order, accept/reject/complete/cancel, complaint. |
| 12.3 | **API documentation (Swagger/OpenAPI)** | 🟡 Sedang | Tidak ada dokumentasi API formal. Generate dari annotation atau tulis manual menggunakan Swagger UI (`l5-swagger`). |
| 12.4 | **Form Request validation** | 🟡 Sedang | Beberapa controller masih validasi inline (`$request->validate`). Pindahkan ke FormRequest classes agar konsisten. |

---

## C. Item Tertunda dari Refactoring Sebelumnya

| # | Item | Status |
|---|------|--------|
| 1 | Komplain publik via web + proteksi spam | ⏳ Tunggu data stabil |
| 2 | Desain ulang skema partner/mitra | ⏳ Tunggu kebutuhan bisnis |

---

## D. Rekomendasi Prioritas Pengerjaan

### Sprint 1 — Fondasi & Keamanan (Kritis)
1. Password reset & change password API (1.1, 1.5)
2. Detail order endpoint + total harga (3.1, 3.5)
3. Get profil lengkap (4.2)
4. Konfirmasi pembayaran (3.2)
5. Suspend/unsuspend seller (5.5)

### Sprint 2 — Pengalaman Pengguna
6. Kategori produk master table (2.1)
7. Stok produk + validasi order (2.4)
8. Notifikasi in-app / inbox (8.1, 8.2)
9. Pencarian produk (2.5)
10. Catatan order + bukti pembayaran (3.3, 3.4)

### Sprint 3 — Fitur Lanjutan
11. Chat buyer ↔ seller (6.1)
12. Voucher / promo (7.1)
13. Review terpisah dari complaint (9.1)
14. Lokasi real-time seller (11.1)
15. Export data admin (5.3)

### Sprint 4 — Quality & Polish
16. Unit & feature tests (12.1, 12.2)
17. API documentation Swagger (12.3)
18. Activity log viewer admin (5.4)
19. Daftar buyer di admin (5.1)
20. Detail order di admin (5.2)
