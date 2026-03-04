# Blueprint Rekomendasi Fitur & Sprint Aplikasi Mobile Dangling

Dokumen ini berisi panduan komprehensif untuk tim Frontend Mobile (Flutter / React Native) dalam mengembangkan aplikasi mobile Dangling. Semua fitur telah disesuaikan agar berjalan selaras dengan kapabilitas **REST API DanglingWeb** yang sudah rampung dibangun.

Aplikasi dibagi menjadi dua fungsionalitas utama yang dapat digabung dalam satu APK/AAB dengan mekanisme Role-Switching, atau dipisah menjadi 2 aplikasi berbeda (Dangling Customer & Dangling Seller).

---

## 📱 Daftar Fitur Inti (Core Features)

### 1. Modul Otentikasi & Akun Umum
*   **Onboarding & Splash Screen:** Animasi awal aplikasi dan pengecekan sesi Token aktif.
*   **Sign Up (Register):** Form pendaftaran akun baru (Nama, Email, Telepon, Password). Default role: `buyer`.
*   **Sign In (Login):** Validasi kredensial. Aplikasi harus menyimpan **Bearer Token** dan **Refresh Token** secara aman (Secure Storage/Keychain). Saat login, mobile akan menembak endpoint FCM Token ke server.
*   **Lupa Password:** Alur input email → Terima Token OTP → Set Password Baru.
*   **Manajemen Profil:** Edit data diri, **Upload/Ganti Foto Profil** avatar, dan tombol "Log Out" serta "Hapus Akun" (Soft Delete).

### 2. Modul Pembeli (Buyer Role)
*   **Beranda (Dashboard):** Menampilkan banner promo (jika ada), kategori produk ("Makanan", "Minuman"), dan widget "Pedagang di Sekitar Anda".
*   **Peta & Pencarian Pedagang (Radius):** 
    *   Sistem meminta akses GPS (Location Permission) perangkat.
    *   Menampilkan UI Map/List penjual terdekat menggunakan API pencarian berdasarkan *latitude/longitude* dengan filter *jarak/radius* dan status `is_online=true`.
*   **Katalog Menu Seller:** Menampilkan profil satu penjual beserta daftar produk (menu) aktifnya yang dikelompokkan berdasarkan *Category*.
*   **Detail Produk:** Menampilkan **Galeri Foto Produk (Multiple Images)**, Harga Asli (Coret jika ada diskon), Deskripsi, dan kontrol kuantitas (+/-) pesanan.
*   **Keranjang Belanja (Cart) & Checkout:**
    *   Keranjang belanja bersifat *local state* (SQLite/Redux/Provider) pada satu pedagang.
    *   Form Checkout: Pilihan tipe pembayaran (COD/Transfer), input "Catatan Pesanan", dan form masukan "Kode Voucher/Promo".
    *   Sistem akan menghitung subtotal dan mengeksekusi POST Order API.
*   **Upload Bukti Bayar:** Untuk metode `TRANSFER`, ada halaman khusus _Upload Image_ struk pembayaran agar divalidasi manual.
*   **Pelacakan Pesanan:** UI transisi status pesanan (`Pending` -> `Accepted` -> `Completed`/`Cancelled`).
*   **Riwayat & Re-Order:** Tab menu untuk melihat *Order History* lama dan satu tombol pintasan untuk "Pesan Ulang" (Reorder).
*   **Pusat Bantuan (Review & Komplain):**
    *   Fitur memberi **Rating Bintang 1-5** setelah transaksi selesai.
    *   Form isian keluhan (Complaint) jika barang tidak sesuai harapan (akan diproses Admin Web).
*   **Wishlist (Favorit):** Menyimpan pedagang langganan agar cepat diakses.
*   **Chat Real-Time:** Ngobrol langsung dengan penjual untuk menanyakan stok atau rute pengiriman (In-App Messaging).

### 3. Modul Pedagang (Seller Role / Mitra)
*   **Upgrade Akun:** Form registrasi mandiri di dalam aplikasi bagi *Buyer* yang ingin beralih menjadi Mitra Dagang.
*   **Toko Saya (Storefront Management):**
    *   Tombol geser (Toggle) besar: **Online / Offline**. Memancarkan posisi GPS pedagang saat ini (Live Tracking ping ke API).
    *   Ubah jam Oprasional dan Informasi toko.
*   **Manajemen Produk (CRUD Inventory):**
    *   Menambah menu baru: Upload banyak foto (`images[]`), atur harga diskon, atur sisa stok fisik.
    *   Tombol "Habis/Tersedia" (`is_active` toggle) untuk mematikan produk dengan sekali tap.
*   **Pos-Kasir (Order Inbox):**
    *   Layar pantauan pesanan masuk (*Pending Orders*). Berdering jika ada push notifikasi baru.
    *   Aksi cepat: **Terima (Accept) / Tolak (Reject)** pesanan beserta alasan pembatalan.
    *   Tombol "Selesaikan Pesanan (Complete)" saat kurir mem-pickup barang atau barang diserahkan ke pembeli.
*   **Pusat Balasan (Reviews & Messages):** Layar tab khusus untuk membalas ulasan pembeli atau membalas Chat pembeli.
*   **Statistik Bisnis (Dashboard Seller):** Ringkasan total uang masuk, performa rating, dan grafik jumlah pesanan dalam bulan ini.

---

## 🗓️ Rekomendasi Urutan Pengerjaan (Sprints)

Agar peluncuran bertahap dan teruji, bagi pengerjaan Frontend Mobile ke dalam 4 Sprint Utama:

### Sprint 1: Pondasi & Auth (Pekan 1)
Sprint ini berfokus pada kerangka kerja dasar UI, Navigasi, dan Manajemen State Lokal (Token Auth).
*   [ ] Inisialisasi Project (Theme, Colors, Typography, Routing/GoRouter/React-Navigation).
*   [ ] Integrasi HTTP Client (Axios/Dio) beserta Interceptor untuk otomatis menyisipkan *Bearer Token* dan *Error Handling* JSON (422/401).
*   [ ] UI/UX Splash Screen & Deteksi Sesi Login.
*   [ ] Slicing UI Form Register, Login, Lupa Password.
*   [ ] Integrasi API Endpoint `/register`, `/login`, `/forgot-password`, `/reset-password`.
*   [ ] Tab Profil Sederhana (Read Data & Upload Foto via `/api/profile/photo`).

### Sprint 2: Alur Pembeli Inti (Core Buyer Flow) (Pekan 2-3)
Inti dari platform ini, memastikan transaksi pembeli ter-submit dari pencarian hingga API pembuatan Order.
*   [ ] Implementasi Layanan Lokasi (GPS Permission) dan UI Google Maps/Mapbox (Atau List View Jarak).
*   [ ] Integrasi Panggilan `GET /api/sellers?lat=..&lng=..`.
*   [ ] Slicing UI Detail Pedagang & Katalog Menu Kategorinya.
*   [ ] Slicing UI Detail Produk (Carousel *Multiple Images* dan kalkulasi harga `original_price`).
*   [ ] Manajemen State Keranjang Belanja (Cart Logic).
*   [ ] Slicing UI Form Checkout: Pilih `COD`/`TRANSFER`, input catatan, kupon, dan hitung diskon via endpoint `/check-voucher`.
*   [ ] Transmisi payload POST `qty` items ke `/api/orders` (Pembuatan Transaksi).
*   [ ] UI Daftar "Pesanan Tertunda" pembeli. Upload Bukti TF (*Confirm Payment* API).

### Sprint 3: Alur Pedagang Inti (Core Seller Flow) (Pekan 3-4)
Membuka aplikasi sisi penjual agar pesanan dari Sprint 2 di atas dapat direspon.
*   [ ] UI Form Upgrade akun menjadi Pedagang.
*   [ ] Switch UI Dashboard (Buyer Mode vs Seller Mode).
*   [ ] Slicing UI Inbox Pesanan Seller (List `Pending` dan `Accepted`).
*   [ ] Fungsionalitas Tombol Terima/Tolak Order (`/api/orders/{id}/accept/reject/complete`).
*   [ ] Fungsionalitas Toggle `is_online` Seller & *Background Location Tracker* pembaruan GPS otomatis.
*   [ ] Slicing UI Product Inventory: Form input multi-foto (`images[]`), nama, harga, stok, dsb.

### Sprint 4: Ekosistem Penunjang (Polish & Relience) (Pekan 5)
Mengisi ruang kosong fitur interaksi dan retensi pengguna.
*   [ ] Integrasi SDK Firebase Cloud Messaging (FCM) dan _Local Notifications_ (Pop-up Push Notif).
*   [ ] Slicing UI Inbox Notifikasi (`GET /api/notifications`).
*   [ ] Slicing UI Obrolan Chat (*Chat Interface*) (`/api/conversations`).
*   [ ] Slicing UI Riwayat Pesanan (Order History) Pembeli & Penjual.
*   [ ] Fungsionalitas Tombol *Re-Order* Pesanan Lama.
*   [ ] Slicing form Rating Bintang 1-5 (`/api/reviews`) dan form Komplain (`/api/complaints`).
*   [ ] Halaman Favorit Pedagang (Wishlist).
*   [ ] UI Dashboard Statistik Penjual (Visualisasi Chart).

---

## 🛠 Panduan Integrasi (Mobile ke API DanglingWeb)

1.  **Form Data (Multipart/form-data):** Selalu ingat untuk menggunakan `FormData` / instance `MultipartFile` pada framework mobile saat menembak endpoint yang mempunyai fitur unggah foto: Profile (Foto akun), Update Pedagang, Confirm Transfer Payment, dan Produk Galeri.
2.  **API Documentation:** Acuan struktur JSON, HTTP Variables, dan daftar endpoint terkini dapat dilihat langsung secara interaktif jika menjalankan Server Backend pada rute: `http://localhost:8000/api/documentation`.
3.  **Exception Handling Khusus:** 
    *   Jika backend mengembalikan HTTP 422 (Unprocessable Entity), _parse_ JSON `errors` nya untuk memunculkan tulisan *Validation Text* merah di bawah TextInput mobile.
    *   Jika backend mengembalikan HTTP 401 (Unauthenticated), lakukan fungsi *Force Logout* otomatis, hapus token secure storage, dan tendang (*redirect*) user kembali ke layer Splash/Login Screen.
4.  **Network Resilience:** Ketika melakukan ping Live GPS pedagang secara kontinu, pasang blok *Timeout Handler* agar jika terjadi *Bad Internet Connection*, pembaruan cukup diabaikan di-latar belakang secara *silent* alih-alih menampilkan *Pop-up Error* mengganggu di layar Pedagang.
