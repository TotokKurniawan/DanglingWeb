# Analisis Kesesuaian & Rekomendasi — DanglingWeb

Dokumen ini memeriksa kesesuaian logika dan fungsi dengan studi kasus marketplace, serta rekomendasi optimasi dan struktur kode.

---

## 1. Kesesuaian dengan Studi Kasus

### Yang sudah sesuai

| Aspek | Status | Keterangan |
|-------|--------|------------|
| Role: Pembeli, Pedagang, Admin, Operator | ✅ | User + Buyer/Seller, login web hanya admin/operator |
| Register → default pembeli | ✅ | AuthController buat User + Buyer |
| Upgrade ke seller | ✅ | SellerController upgradeToSeller, role jadi pedagang |
| Produk per seller | ✅ | Product id_pedagang, ProductController scope seller |
| Order: buat, terima, tolak, selesai | ✅ | OrderController + OrderStatusController |
| Riwayat order buyer & seller | ✅ | OrderHistoryController filter by buyer/seller |
| Status toko online/offline | ✅ | Seller status, GET/POST store-status |
| Lokasi seller (lat/long) | ✅ | LocationController |
| Keluhan (form web) | ✅ | ComplaintController store, nullable buyer/seller |
| Panel admin: dashboard, seller, keluhan, operator | ✅ | AdminController |
| Panel operator: dashboard, seller, keluhan | ✅ | OperatorController |
| Mitra (CRUD) | ✅ | PartnerController, Form Requests |
| Profil buyer/seller (API) | ✅ | ProfileController updateBuyerProfile, updateSellerProfile |
| Daftar seller online + produk (API) | ✅ | SellerProductController getAllSellers, getSellerById |

### Yang belum sesuai / celah logika

Semua celah di bawah ini **sudah diperbaiki** (lihat commit / tahap 1–3).

| Celah | Status | Solusi |
|-------|--------|--------|
| Order tidak punya detail item | ✅ Diperbaiki | Tabel `order_items`, create order dengan `items[]`, response load `orderItems.product`. |
| Pembeli tidak bisa batalkan order | ✅ Diperbaiki | Endpoint `PUT /api/orders/{id}/cancel-by-buyer` (hanya status Menunggu). |
| Produk tidak bisa edit/hapus (API) | ✅ Diperbaiki | `PUT /api/products/{id}`, `DELETE /api/products/{id}`. |
| Web admin/operator tidak dilindungi | ✅ Diperbaiki | Middleware `auth` + `role:admin` / `role:operator` pada route. |
| Model History legacy | ✅ Diperbaiki | `history.php` dihapus; pakai model `Order` saja. |
| Complete order tanpa validasi status | ✅ Diperbaiki | Hanya dari status Diterima → Selesai. |
| Cancel order hanya seller / tanpa cek status | ✅ Diperbaiki | Seller: Menunggu/Diterima; buyer: Menunggu; validasi di controller. |
| getSellerById menampilkan seller offline | ⚪ Diterima | By design (untuk riwayat); bisa dibatasi nanti jika perlu. |
| Complaint tanpa auth (form web) | ⚪ Diterima | Form publik + throttle 5/menit; API complaint dengan auth + validasi order opsional. |

---

## 2. Optimasi Logika & Pengelolaan Sistem

**Status implementasi:** ✅ Keamanan (middleware), ✅ Order/transaksi (order_items, aturan status), ✅ Produk (update/delete), ✅ Konsistensi (whereYear, profil seller nama_toko), ✅ Throttle complaint web, ✅ DashboardStatsService, ✅ API complaint dengan validasi opsional. Lihat README untuk endpoint terbaru.

### 2.1 Keamanan & otorisasi

- **Lindungi route web panel**
  - Grup route `admin` dan `operator` dengan `middleware(['auth', ...])`.
  - Gunakan middleware role, mis. `CheckRole:admin` dan `CheckRole:operator`, atau satu middleware yang cek `in_array($user->role, ['admin', 'operator'])` lalu sub-cek untuk admin-only (operators, profile admin).
- **Pastikan hanya admin yang bisa**
  - Buat operator, update/hapus user, akses halaman admin-only (mis. operators, profile admin).
- **API**
  - Tetap pakai `auth:api` untuk route yang sudah dilindungi; tambah pengecekan kepemilikan (buyer/seller) seperti yang sudah ada di controller.

### 2.2 Order & transaksi

- **Detail order (order items)**
  - Tambah tabel `order_items`: `order_id`, `product_id`, `qty`, `harga_saat_order` (snapshot).
  - Saat create order: terima array item (product_id + qty), validasi produk milik seller yang dipilih, isi `order_items` dan hitung/total jika perlu.
  - Response order (termasuk order-history) load relasi `orderItems.product` agar frontend punya detail.
- **Aturan status order**
  - **Accept:** hanya dari `Menunggu`.
  - **Reject:** hanya dari `Menunggu`.
  - **Complete:** hanya dari `Diterima`.
  - **Cancel:** 
    - Seller: boleh untuk `Menunggu` atau `Diterima` (sesuai kebijakan).
    - Buyer: boleh untuk `Menunggu` (endpoint baru, mis. `PUT /api/orders/{id}/cancel-by-buyer`).
  - Di controller, sebelum update status: `if ($order->status !== Order::STATUS_PENDING) return $this->error(...)` (dan sejenisnya).

### 2.3 Produk

- **API produk**
  - `PUT /api/products/{id}` — update (nama, harga, kategori, foto opsional); validasi produk milik seller yang login.
  - `DELETE /api/products/{id}` — soft delete atau hard delete; validasi kepemilikan.
- **Validasi**
  - Harga >= 0, kategori max length, foto optional pada update.

### 2.4 Konsistensi data & bug kecil

- **OperatorController dashboard**
  - `whereYear('created_at', now())` → sebaiknya `whereYear('created_at', now()->year)` agar konsisten dengan AdminController dan tidak bergantung pada tipe Carbon di parameter kedua.
- **Profil seller (API)**
  - ProfileController::updateSellerProfile pakai input `nama` untuk update `namaToko`. Dokumentasikan atau samakan nama field (nama_toko/namaToko) di request.
- **Hapus atau align model History**
  - Hapus `app/Models/history.php` jika tidak dipakai, atau ubah relasi ke `Buyer`/`Seller` dan jadikan alias dari Order; hindari duplikasi model untuk tabel yang sama.

### 2.5 Keluhan (complaint)

- Opsional: endpoint API untuk submit keluhan (setelah login) dengan `id_pembeli` dari user dan validasi bahwa pernah ada order dengan seller tersebut.
- Batasi rate submit (throttle) untuk form publik.

### 2.6 Lain-lain

- **Response order**
  - Saat mengembalikan order (create, accept, reject, complete, cancel, order-history), load relasi `buyer`, `seller`, dan nanti `orderItems` agar client tidak perlu request berulang.
- **Database**
  - Gunakan transaksi DB saat create order + order_items agar konsisten.

---

## 3. Struktur Folder & Kode

**Status implementasi Tahap 3:** ✅ Form Request API (CreateOrder, Store/UpdateProduct, StoreComplaint, RejectOrder). ✅ Form Request Web dipindah ke `Requests/Web/` (Mitra, Operator). ✅ Controller API & Web memakai Form Request. ✅ Satu folder Controllers/Web (tidak ada duplikat `web` di Windows).

### 3.1 Masalah struktur saat ini

- **Duplikasi path case**
  - Ada `app/Http/Controllers/Web/` dan `app\Http\Controllers\web\` (huruf W besar/kecil). Di Windows bisa tidak ketara, di Linux production bisa error.
  - Ada `app/Models/` dan `app\Models\`; `database/migrations` vs `database\migrations`. Sebaiknya satu konvensi (PascalCase untuk namespace, path kecil untuk folder Laravel).
- **File duplikat/legacy**
  - `app/Models/history.php` (History, Pembeli, Pedagang) vs `app/Models/Order.php` (Order, Buyer, Seller). Satu model untuk satu tujuan (Order untuk histories).
  - Duplikat controller/migration (web vs Web, dll.) — hapus yang tidak dipakai dan pastikan route memakai satu namespace (mis. `App\Http\Controllers\Web`).
- **Controller terlalu gemuk**
  - AdminController/OperatorController penuh logika aggregasi (grafik, persentase). Lebih baik pindah ke Service atau helper agar controller hanya koordinasi request–response.
- **Validasi**
  - Web: beberapa pakai Form Request (Mitra, Operator), sebagian inline di controller. API: hampir semua validasi inline di controller. Sulit dipakai ulang dan testing.

### 3.2 Rekomendasi struktur

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/                    # Tetap per resource
│   │   │   ├── AuthController.php
│   │   │   ├── OrderController.php
│   │   │   ├── OrderStatusController.php
│   │   │   ├── OrderHistoryController.php
│   │   │   ├── ProductController.php
│   │   │   ├── SellerController.php
│   │   │   ├── SellerProductController.php
│   │   │   ├── ProfileController.php
│   │   │   └── LocationController.php
│   │   └── Web/
│   │       ├── AdminController.php
│   │       ├── OperatorController.php
│   │       ├── ComplaintController.php
│   │       ├── LandingController.php
│   │       ├── LoginController.php
│   │       ├── PartnerController.php
│   │       ├── ProfileAdminController.php
│   │       ├── ProfileOperatorController.php
│   │       ├── SellerController.php   # Web: update status seller
│   │       └── UserController.php
│   ├── Requests/                   # Form Requests (API + Web)
│   │   ├── Api/
│   │   │   ├── CreateOrderRequest.php
│   │   │   ├── UpdateProductRequest.php
│   │   │   └── ...
│   │   └── Web/
│   │       ├── MitraRequest.php
│   │       └── ...
│   ├── Middleware/
│   │   ├── EnsureUserIsAdmin.php
│   │   └── EnsureUserIsOperator.php  # atau satu EnsureWebRole
│   └── Traits/
│       └── ApiResponse.php
├── Models/
│   ├── User.php
│   ├── Buyer.php
│   ├── Seller.php
│   ├── Product.php
│   ├── Order.php
│   ├── OrderItem.php               # (baru jika tambah detail order)
│   ├── Complaint.php
│   └── Partner.php
│   # Hapus: history.php (pakai Order saja)
├── Services/                       # Opsional, untuk logika berat
│   ├── OrderService.php            # createOrder + aturan status
│   ├── DashboardStatsService.php  # aggregasi dashboard admin/operator
│   └── ...
```

### 3.3 Rekomendasi kode

- **Satu namespace Web**
  - Route memakai `App\Http\Controllers\Web` (huruf W besar). Di Windows hanya satu folder `Web`; di Linux pastikan tidak ada folder `web` (lowercase) terpisah.
- **Form Request untuk API** ✅
  - `App\Http\Requests\Api\`: CreateOrderRequest, StoreProductRequest, UpdateProductRequest, StoreComplaintRequest, RejectOrderRequest. Controller API memakai Form Request; validasi terpusat.
- **Form Request untuk Web** ✅
  - `App\Http\Requests\Web\`: MitraRequest, UpdateMitraRequest, TambahOperatorRequest, UpdateOperatorRequest. PartnerController & UserController memakai namespace Web.
- **Middleware web**
  - Route group:
    - `Route::middleware(['auth'])->prefix('admin')->middleware(EnsureUserIsAdmin::class)->group(...)`
    - `Route::middleware(['auth'])->prefix('operator')->middleware(EnsureUserIsOperator::class)->group(...)`
  - Redirect ke login jika belum auth, ke home atau 403 jika role salah.
- **Service layer (opsional)**
  - `OrderService::createOrder(User $user, array $data)` — validasi buyer, seller, buat Order + OrderItems dalam DB::transaction.
  - `OrderService::accept(Order $order, User $user)` — cek kepemilikan + status, lalu update.
  - `DashboardStatsService::getStatsForAdmin()` / `getStatsForOperator()` — return array untuk view; kurangi logika di AdminController/OperatorController.
- **Model**
  - Order: relasi `orderItems()` hasMany; method helper `canBeAccepted()`, `canBeCancelledBySeller()`, `canBeCancelledByBuyer()` (opsional).
  - Hapus `History`; semua pakai `Order` dengan table `histories`.

### 3.4 Naming & konvensi

- **Tabel/kolom**
  - Sudah ada migrasi snake_case; konsisten pakai snake_case di DB, camelCase hanya di API response jika diinginkan (README saat ini bilang snake_case).
- **Route naming**
  - Tetap pakai `admin.*`, `operator.*`, `partners.*` seperti sekarang; konsisten untuk redirect dan `route()`.

---

## 4. Ringkasan prioritas

| Prioritas | Aksi |
|-----------|------|
| Kritis | Lindungi route admin/operator dengan `auth` + middleware role. |
| Tinggi | Tambah order_items + logika create order dengan detail item; validasi aturan status (accept/reject/complete/cancel). |
| Sedang | Pembeli bisa cancel order (status Menunggu); API update/delete produk; hapus/align model History; perbaiki bug whereYear di OperatorController. |
| Rendah | Service layer & Form Request API; response order include relation; dokumentasi field profil seller. |

Dengan langkah di atas, logika dan fungsi akan selaras dengan studi kasus, keamanan web panel terjamin, dan struktur folder serta kode lebih rapi dan mudah dikembangkan.

---

## 5. Checklist kondisi project (audit terakhir)

Gunakan checklist ini untuk mengecek apakah project sudah maksimal menurut dokumentasi.

### Sudah selesai ✅

| Aspek | Keterangan |
|-------|------------|
| **Celah logika (point 1)** | Order items, pembeli cancel, produk CRUD, middleware admin/operator, History dihapus, aturan status order. |
| **Optimasi (point 2)** | Keamanan route, throttle complaint web, DashboardStatsService, API complaint, profil seller nama_toko, response order + orderItems. |
| **Struktur (point 3)** | Form Request API (CreateOrder, Store/UpdateProduct, StoreComplaint, RejectOrder). Form Request Web (Mitra, Operator, StoreComplaintWeb). Satu namespace Web. |
| **Model Order** | Relasi orderItems(); helper isPending(), isAccepted(), canBeAccepted(), canBeCancelledBySeller(), canBeCancelledByBuyer(). Controller pakai helper ini. |
| **Web complaint** | Form Request StoreComplaintWebRequest; KeluhanRequest (tidak terpakai) dihapus. |

### Opsional / bisa dimaksimalkan lagi

| Item | Manfaat | Effort |
|------|---------|--------|
| **OrderService** | Pindah logika create order + aturan status ke `App\Services\OrderService`; controller hanya panggil service. Konsisten dengan DashboardStatsService. | Sedang |
| **Middleware nama eksplisit** | Tambah `EnsureUserIsAdmin` / `EnsureUserIsOperator` yang dalamnya panggil CheckRole, agar route pakai nama yang lebih deskriptif. | Kecil |
| **getSellerById hanya online** | Jika kebijakan: buyer hanya lihat detail seller yang online; tambah filter `where('status','online')` di getSellerById atau endpoint terpisah. | Kecil |
| **Unit / feature test** | Test Form Request, OrderService, aturan status order, auth. | Besar |
| **API resource (optional)** | Laravel API Resource untuk response order/product/seller agar format konsisten dan mudah diubah. | Sedang |
| **CORS & env** | Pastikan `config/cors.php` dan `.env` untuk production (APP_URL, CORS, rate limit). | Kecil |

### Kesimpulan

Project **sudah memenuhi** rekomendasi utama di dokumen analisis (point 1–3). Yang tersisa adalah peningkatan **opsional** (OrderService, test, API Resource, kebijakan getSellerById) untuk maintainability dan skala lanjutan.
