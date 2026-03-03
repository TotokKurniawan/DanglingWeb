# Daftar Fitur dan Alur Sistem DanglingWeb

Sistem DanglingWeb terdiri dari dua komponen antarmuka utama:
1. **REST API (Mobile App)** — Diperuntukkan bagi pengguna publik (Buyer) dan Mitra Pedagang (Seller).
2. **Web Panel (Admin/Operator)** — Diperuntukkan bagi staf internal atau Admin untuk memonitor, mengelola data, dan meninjau masalah.

Berikut adalah uraian lengkap dari setiap fitur dan alur (flow) kerjanya.

---

## A. Fitur API (Mobile App)

API menggunakan otentikasi berbasis token (Laravel Passport) dengan format `Bearer Token`. Fitur dibagi menjadi dua peran besar: _Buyer_ dan _Seller_.

### 1. Otentikasi & Akun
- **Register Buyer:** Pengguna mendaftar dengan email, nama, phone, dan password. Akan otomatis mendapatkan peran (Role) sebagai `buyer`.
- **Login:** Pengguna bisa login dengan email & password untuk mendapatkan Token Akses.
- **Logout:** Token akan dicabut (revoke) dari sisi server.
- **Profil:** Pengguna bisa mengedit profil (nama, telepon, alamat) dan mengunggah foto profil (dengan _storage link_).
- **Notifikasi Push (Device Token):** Mobile app bisa mendaftarkan FCM (Firebase Cloud Messaging) Device Token saat login agar server dapat menembak push notifikasi jika ada kejadian masuk (chat, keluhan, status pesanan baru).

### 2. Fitur _Buyer_ (Pembeli)
- **Pencarian Pedagang (Sellers):**
  - **Flow:** Buyer memuat data pedagang terdekat (_Haversine formula_) menggunakan koordinat GPS `lat` & `lng`. Bisa memfilter jarak `radius` (km), hanya pedagang yang sedang online (`is_online = true`), maupun mengurutkan (sort) berdasarkan *rating*, *jarak*, atau *nama*.
- **Listing Produk:**
  - **Flow:** Setelah memilih satu pedagang, Buyer disajikan daftar Menu/Produk yang secara spesifik diatur `is_active = true` oleh Seller tersebut. Produk juga bisa dikategorikan (misal: "Makanan", "Minuman").
- **Favorit (Wishlist):**
  - **Flow:** Buyer menyukai pedagang tertentu sehingga tersimpan di _Favorites_ untuk diakses cepat di beranda.
- **Checkout Pesanan (Order Flow):**
  - **Flow:** 
    1. Buyer memilih produk & *quantity* ke dalam keranjang, memasang catatan (opsional, misal "Pedas").
    2. Menentukan metode pembayaran (`COD` / `TRANSFER`).
    3. Jika memiliki kode _Voucher/Promo_, pembeli dapat memasukkan kode tersebut (Sistem akan memvalidasi limit, dan minimum belanja).
    4. Pesanan tercipta dengan status `pending`. Stok produk jualan Seller akan di-_reserve_ (dipotong sementara).
    5. *(Rules Cancel)*: Buyer berhak membatalkan pesanan maksimal X menit (diatur dari admin) selama status masih `pending`.
- **Pesanan Ulang (Re-Order):**
  - **Flow:** Buyer bisa menekan tombol "Re-order" pada pesanan sejarah (_history_) lama yang sudah `completed`. Sistem akan men-_clone_ order lama menjadi order _pending_ baru ke Seller yang sama.
- **Diskusi & Chat Interaktif:**
  - **Flow:** Buyer dapat memulai `Message` real-time ke Seller via in-app API untuk menanyakan rute kurir/ketersediaan barang. (Tersambung ke riwayat `Conversation`).
- **Rating/Review & Komplain:**
  - **Flow Review:** Setelah pesanan berstatus `completed` atau `accepted` selesai, pembeli dapat memberikan Bintang 1-5. Nilai ini menjadi _average rating_ seller secara publik.
  - **Flow Komplain:** Jika pesanan bermasalah, Buyer dapat mengirim Komplain terpisah dari rating, yang nantinya ditangani (resolusi) oleh pihak Admin Web.

### 3. Fitur _Seller_ (Pedagang)
- **Upgrade ke Pedagang:**
  - **Flow:** Akun Buyer dapat _apply_ menjadi Seller dengan mendaftarkan nama toko, titik koordinat awal, dan detail lain. Sistem mencatat jam buka-tutup (`open_time`, `close_time`).
- **Toggle Shift Kerja:**
  - **Flow:** Seller beroperasi secara fleksibel. Dapat mematikan dan menghidupkan toko lewat tombol Online/Offline (`is_online` status) kapan saja secara real-time. Koordinat lokasi (GPS) Seller bisa di_update_ berkala ke backend.
- **Manajemen Menu/Katalog:**
  - **Flow:** Seller bisa menambah stok menu, mengubah harga, mengunggah foto, dan mematikan menu secara _toggle_ (`is_active` = false) jika stok hidangan sedang ludes/kosong sesaat.
- **Pengelolaan Pesanan Masuk:**
  - **Flow:**
    1. Pesanan baru tampil di Inbox order. Seller memiliki waktu (timeout) merespon pesanan tersebut.
    2. Jika diterima (`accepted`), Buyer mendapat notifikasi Order Diproses. Jika ditolak (`rejected` / `cancelled`), stok di-_refund_ ke inventaris produk secara otomatis.
    3. Setelah dikurir dan pesanan selesai, pesanan ditandai selesai secara manual (atau via Auto-Complete _Scheduler_ jika terlelap).
- **Balas Ulasan (Seller Reply):** Seller dapat memberikan respons klarifikasi kepada Rating & Komentar Buyer di etalasenya.
- **Statistik Penjualan (Dashboard):** Akses Endpoint untuk melaporkan ringkasan _Total Penjualan_, _Income_, _Avg Rating_, dan _Traffic_ Order di rentang waktu harian/bulanan.

---

## B. Fitur Web Panel (Admin Dashboard)

Web Panel dijalankan secara stateful (Sessions Laravel) dan dilindungi oleh role *Admin*.

- **Dashboard Analytic:** Menampilkan kalkulasi pendapatan platform, Grafik total order harian/mingguan, metrik aduan/keluhan yang antri (Pending Complaints), dan "Top 5 Seller Terlaris".
- **Daftar Customer (Buyer):** Mengelola master-data publik. Melihat detail kontak semua pengunduh aplikasi.
- **Daftar Pedagang (Seller):**
  - **Manage:** _Approve_/_Suspend_ layanan pedagang yang terbukti melanggar (sehingga tidak muncul di API umum).
  - **Export Data:** Unduh rekap CSV pendaftar Mitra (Laporan harian).
- **Riwayat Pesanan Global:** Memantau letak pergerakan _Order_ seluruh transaksi sistem, metode pembayaran, hingga bukti transfer.
- **Monitoring Keluhan (Resolusi Konflik):**
  - **Flow:** Admin rutin meninjau aduan. Mengubah status aduan dari `open` → `in_progress` → `resolved`.
  - Admin melakukan verifikasi manual antara keterangan Pembeli dan Penjual via log chat jika diperlukan.
- **Activity Log Viewer (Audit Trail):** Penelusuran aktivitas krusial sistem. Melihat jejak jika order sengaja dibatalkan, siapa validatornya (Seller/Admin), atau perubahan status komplain beserta IP _address_ subjek.
- **Kategori & Voucher Global:** 
  - Admin mendaftarkan (CRUD) master kategori (Makanan, Minuman, Sembako).
  - Admin menebar "Kode Promo/Voucher Diskon" lengkap dengan kuota `limit`, minimal pembelian, persenan diskon dan *Expiration date*.
- **Konfigurasi Fundamental (Settings):** Setup dinamis pengelolan variabel sistem tanpa perlu masuk SSH server. Meliputi:
  - Waktu `timeout` pembatalan bagi buyer (menit).
  - Radius maksimal _default_ pencarian (`radius` km).
  - Pengumuman Global Banner (berjalan di atas layar apps).
  - Mode Pemeliharaan (_Maintenance Mode_).

---

## C. Alur Proses Cron / Latar Belakang

Aplikasi modern ini tidak mengandalkan eksekusi klik semata. Ada _scheduler_ dan _queue_ yang berputar terus-menerus:
1. **Auto-Timeout Order (Pending):** Apabila ada order *pending* yang dihiraukan/tidak dijawab oleh *Seller* dalam kurun waktu lebih dari ketentuan, cron script akan meresetnya menjadi `cancelled` agar stok tidak menggantung.
2. **Auto-Complete Order (Accepted):** Order yang terkirim namun lupa di-closing (status masih `accepted` terlalu berhari-hari), dipaksa selesai (`completed`) otomatis dan merilis saldo secara logis.
3. **Queue Notifications:** Email massal atau Blast Notifikasi Push FCM selalu diletakan di Queue Worker agar API _Create Order_ atau _Review_ tidak melambat menunggu *Handshake TLS* GCM/Firebase Google.
