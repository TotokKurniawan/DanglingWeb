# Analisis & Rekomendasi — Item Belum Dikerjakan

Terakhir diperbarui: **3 Maret 2026**

> Modul 1–8, push notification, dan auto-cancel/auto-complete sudah selesai. Dokumen ini hanya mencatat item yang **ditangguhkan**.

---

## 1. Komplain Publik via Web (Modul 4)

**Prasyarat:** Data complaint dari buyer terautentikasi sudah stabil

Langkah pengerjaan:
1. Aktifkan kembali endpoint `POST /complaints` (web, tanpa login).
2. Tambahkan proteksi: rate limiting (throttle), captcha (reCAPTCHA), dan moderasi dasar.
3. Pisahkan complaint publik dari complaint terautentikasi (field `source = 'web'` / `'app'`).
4. Tambahkan halaman daftar complaint publik di panel admin.

---

## 2. Desain Ulang Skema Partner/Mitra (Modul 8)

**Prasyarat:** Kebutuhan bisnis partner/mitra sudah jelas

Langkah pengerjaan:
1. Definisikan relasi partner dengan seller (apakah partner = penyedia modal? penyedia lokasi?).
2. Desain ulang tabel `partners` sesuai kebutuhan bisnis baru.
3. Buat flow bisnis partner yang terintegrasi ke order/pendapatan.

---

## Rekomendasi Prioritas

| Prioritas | Item | Dampak |
|-----------|------|--------|
| 🟢 Rendah | Komplain publik via web | Fitur tambahan — tunggu data stabil |
| 🟢 Rendah | Skema partner/mitra | Bisnis — tunggu kebutuhan jelas |
