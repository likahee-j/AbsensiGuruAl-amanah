# PRD — Sistem Absensi Guru Berbasis Web (Laravel)

> **Versi:** 1.0  
> **Tanggal:** Mei 2026  
> **Stack:** Laravel 11, MySQL, Tailwind CSS, JavaScript (Vanilla)

---

## 1. Latar Belakang

Sekolah membutuhkan sistem absensi digital yang menggantikan absensi manual. Guru cukup membuka web di HP, login, scan QR, dan GPS divalidasi secara otomatis. Admin dapat memantau rekap kehadiran secara real-time dari laptop.

---

## 2. Tujuan Produk

- Mempermudah proses absensi guru tanpa perangkat tambahan (fingerprint, mesin khusus)
- Mencegah titip absen dengan kombinasi QR dinamis + validasi GPS
- Memberikan rekap kehadiran otomatis untuk keperluan administrasi

---

## 3. Pengguna (Aktor)

| Aktor | Deskripsi |
|-------|-----------|
| **Guru** | Melakukan absensi harian via HP |
| **Admin / Kepala Sekolah** | Memantau dan mengunduh rekap absensi |

---

## 4. Fitur Utama

### 4.1 Autentikasi

- Login dengan email dan password
- Role: `guru` dan `admin`
- Session berbasis Laravel Breeze / Sanctum
- Halaman login mobile-friendly

### 4.2 QR Code Dinamis (Admin)

- Admin membuka halaman QR di layar/proyektor kelas
- QR di-generate ulang otomatis setiap **30 detik** (via JavaScript polling)
- QR berisi token terenkripsi yang disimpan sementara di database (berlaku 30 detik)
- Library: `simplesoftwareio/simple-qrcode`

### 4.3 Scan QR (Guru)

- Guru membuka kamera dari browser HP (Web API: `getUserMedia`)
- Library scan: `html5-qrcode` (JavaScript)
- Setelah scan berhasil, token QR dikirim ke server untuk divalidasi

### 4.4 Validasi GPS

- Setelah scan QR valid, browser meminta izin lokasi (`navigator.geolocation`)
- Server memvalidasi koordinat guru terhadap koordinat sekolah
- Toleransi radius: **200 meter** (dapat dikonfigurasi admin)
- Jika di luar radius → absensi ditolak + pesan error

### 4.5 Pencatatan Absensi

- Absensi dicatat dengan status: `hadir`, `terlambat`, `tidak hadir`
- Logika terlambat: jam absensi > jam masuk yang dikonfigurasi (default 07:30)
- Data yang disimpan: `guru_id`, `tanggal`, `jam_absensi`, `latitude`, `longitude`, `status`, `qr_token`

### 4.6 Dashboard Admin

- Tabel rekap absensi harian dengan filter: tanggal, nama guru, status
- Statistik ringkas: total hadir, terlambat, tidak hadir (hari ini)
- Export ke **Excel** (`maatwebsite/excel`) dan **PDF** (`barryvdh/laravel-dompdf`)
- Tampilan responsif untuk laptop/desktop

### 4.7 Manajemen Data (Admin)

- CRUD data guru (nama, email, nomor HP, foto)
- Pengaturan lokasi sekolah (koordinat GPS, radius toleransi)
- Pengaturan jam masuk sekolah

---

## 5. Alur Lengkap (User Flow)

```
[GURU]
Buka web → Login → Halaman Absensi → Klik "Mulai Absen"
→ Kamera aktif → Arahkan ke QR → QR ter-scan
→ Izin GPS diminta → GPS valid? 
    ✓ YA  → Absensi berhasil (tampil konfirmasi + jam)
    ✗ TIDAK → Pesan error "Anda di luar area sekolah"

[ADMIN]
Login → Dashboard → Lihat rekap hari ini
→ Filter tanggal/guru → Export Excel/PDF
→ Kelola guru, koordinat sekolah, jam masuk
```

---

## 6. Struktur Database

### Tabel `users`
```
id, name, email, password, role (guru|admin), phone, photo, timestamps
```

### Tabel `attendances`
```
id, user_id (FK), date, check_in_time, status (hadir|terlambat|tidak_hadir),
latitude, longitude, qr_token_used, timestamps
```

### Tabel `qr_tokens`
```
id, token (string, unique), expires_at, is_used, timestamps
```

### Tabel `school_settings`
```
id, school_name, latitude, longitude, radius_meters,
check_in_start, check_in_end, late_threshold, timestamps
```

---

## 7. Struktur Halaman

| Halaman | URL | Akses |
|---------|-----|-------|
| Login | `/login` | Public |
| Absensi (Guru) | `/absensi` | Guru |
| Riwayat Absensi | `/riwayat` | Guru |
| Dashboard | `/admin` | Admin |
| Rekap Absensi | `/admin/rekap` | Admin |
| QR Generator | `/admin/qr` | Admin |
| Manajemen Guru | `/admin/guru` | Admin |
| Pengaturan Sekolah | `/admin/pengaturan` | Admin |

---

## 8. Requirement Teknis

| Komponen | Pilihan |
|----------|---------|
| Framework | Laravel 11 |
| Auth | Laravel Breeze |
| Frontend | Blade + Tailwind CSS + Vanilla JS |
| Database | MySQL 8 |
| QR Generate | simplesoftwareio/simple-qrcode |
| QR Scan | html5-qrcode (CDN) |
| Excel Export | maatwebsite/laravel-excel |
| PDF Export | barryvdh/laravel-dompdf |
| GPS | Browser Geolocation API |
| Jadwal (opsional) | Laravel Scheduler (absensi otomatis "tidak hadir") |

---

## 9. Keamanan

- QR token berbasis UUID + HMAC, expired 30 detik, single-use
- GPS divalidasi di server (bukan hanya client)
- Middleware auth + role pada semua route terlindungi
- CSRF protection bawaan Laravel pada semua form
- Rate limiting pada endpoint absensi (max 5 request/menit per user)

---

## 10. Non-Functional Requirements

- Halaman absensi guru wajib mobile-first (tampil baik di layar 360px+)
- Waktu scan-to-confirm < 3 detik pada koneksi 4G normal
- QR refresh tidak boleh reload halaman (AJAX/fetch)

---

---

# PROMPT UNTUK CLAUDE CODE (VS CODE)

Salin prompt di bawah ini dan jalankan di Claude Code terminal VS Code Anda.

---

## PROMPT UTAMA

```
Saya ingin membangun sistem web absensi guru menggunakan Laravel 11. 
Tolong bantu saya dari awal hingga selesai sesuai spesifikasi berikut.

---

## STACK TEKNOLOGI
- Laravel 11
- MySQL
- Laravel Breeze (auth)
- Blade + Tailwind CSS
- Vanilla JavaScript
- Package: simplesoftwareio/simple-qrcode, maatwebsite/laravel-excel, barryvdh/laravel-dompdf

---

## FITUR YANG HARUS DIBANGUN

### 1. Setup & Auth
- Install Laravel 11 baru
- Install Laravel Breeze dengan Blade + Tailwind
- Tambahkan kolom `role` (enum: guru, admin), `phone`, `photo` ke tabel users
- Buat seeder: 1 admin dan 3 guru dummy
- Buat middleware `CheckRole` untuk proteksi route guru vs admin

### 2. Database & Models
Buat migration dan model untuk:

**qr_tokens**: id, token (string unique), expires_at (timestamp), is_used (boolean default false), timestamps

**attendances**: id, user_id (FK ke users), date (date), check_in_time (time nullable), status (enum: hadir, terlambat, tidak_hadir), latitude (decimal 10,7 nullable), longitude (decimal 10,7 nullable), qr_token_used (string nullable), timestamps

**school_settings**: id, school_name, latitude (decimal 10,7), longitude (decimal 10,7), radius_meters (integer default 200), check_in_start (time), check_in_end (time), late_threshold (time), updated_at

Tambahkan relasi di model User: hasMany Attendance. 
Tambahkan relasi di model Attendance: belongsTo User.

### 3. QR Code System (Admin)
Buat halaman `/admin/qr`:
- Generate QR code setiap 30 detik otomatis tanpa reload halaman
- QR berisi token UUID yang disimpan di tabel qr_tokens dengan expires_at = now() + 30 detik
- API endpoint GET `/api/qr/generate` yang:
  - Hapus token lama yang sudah expired
  - Buat token baru, simpan ke DB, kembalikan sebagai JSON
  - Generate QR dari token tersebut (base64 SVG)
- Tampilan halaman QR besar, dengan countdown timer, cocok untuk ditampilkan di layar proyektor

### 4. Halaman Absensi Guru
Buat halaman `/absensi` (mobile-first):
- Tombol "Mulai Absensi" 
- Saat diklik: aktifkan kamera menggunakan library html5-qrcode dari CDN
- Setelah QR ter-scan:
  - Kirim POST ke `/absensi/scan` dengan token dari QR
  - Server validasi: token ada di DB, belum expired, belum dipakai
  - Jika valid: minta koordinat GPS dari browser
  - Kirim POST ke `/absensi/submit` dengan token + lat + lng
  - Server validasi GPS: hitung jarak dari koordinat sekolah (Haversine formula)
  - Jika dalam radius: simpan absensi, tandai token as used, return sukses
  - Tampilkan konfirmasi: jam masuk, status (hadir/terlambat), nama guru
  - Jika gagal: tampilkan pesan error yang jelas

### 5. Dashboard Admin
Buat halaman `/admin` dengan:
- 4 kartu statistik hari ini: Total Guru, Hadir, Terlambat, Belum Absen
- Tabel absensi hari ini: No, Nama Guru, Jam Masuk, Status, Koordinat
- Filter by tanggal (default hari ini)
- Badge warna status: hijau=hadir, kuning=terlambat, merah=tidak hadir

### 6. Rekap Absensi
Buat halaman `/admin/rekap`:
- Filter: dari tanggal, sampai tanggal, nama guru
- Tabel rekap dengan kolom: Nama, Tanggal, Jam, Status
- Tombol Export Excel (download file .xlsx)
- Tombol Export PDF (download file .pdf)
- Implementasi ExportAttendance menggunakan maatwebsite/laravel-excel
- Implementasi PDF menggunakan barryvdh/laravel-dompdf dengan view blade terpisah

### 7. Manajemen Guru (Admin)
Buat CRUD di `/admin/guru`:
- List semua guru dengan pagination
- Form tambah guru (name, email, password, phone)
- Edit guru
- Hapus guru (soft delete atau hard delete)

### 8. Pengaturan Sekolah (Admin)
Buat halaman `/admin/pengaturan`:
- Form: nama sekolah, latitude, longitude, radius (meter), jam masuk mulai, jam masuk selesai, batas terlambat
- Tampilkan peta mini Leaflet.js (CDN) untuk memilih koordinat sekolah dengan klik
- Simpan ke tabel school_settings (1 row, update jika sudah ada)

### 9. Riwayat Absensi (Guru)
Buat halaman `/riwayat` untuk guru yang login:
- Tabel riwayat absensi milik guru tersebut
- Filter bulan dan tahun
- Ringkasan: total hadir, terlambat, tidak hadir bulan ini

---

## INSTRUKSI PENGERJAAN

1. Mulai dari setup project Laravel 11 baru
2. Kerjakan step by step, satu fitur selesai sebelum ke fitur berikutnya
3. Selalu jalankan `php artisan migrate` setelah membuat migration baru
4. Pastikan semua route dilindungi middleware auth dan role yang sesuai
5. Gunakan Tailwind CSS untuk semua styling, mobile-first untuk halaman guru
6. Buat semua response API dalam format JSON
7. Tambahkan validasi server-side pada semua form dan endpoint
8. Implementasi Haversine formula di PHP untuk kalkulasi jarak GPS
9. Gunakan Laravel config atau school_settings untuk semua nilai yang bisa dikonfigurasi
10. Setelah selesai semua, jalankan `php artisan db:seed` dan tampilkan kredensial login

---

## KREDENSIAL SEEDER (TARGET)
Admin: admin@sekolah.sch.id / password123
Guru 1: budi@sekolah.sch.id / password123
Guru 2: sari@sekolah.sch.id / password123
Guru 3: andi@sekolah.sch.id / password123

Mulai dari langkah 1: buat project Laravel 11 baru dan setup awal.
```

---

## PROMPT LANJUTAN (jika perlu per fitur)

Jika ingin mengerjakan satu fitur tertentu saja, gunakan salah satu prompt ini:

**Hanya QR System:**
```
Saya punya project Laravel 11 yang sudah ada auth dan migration-nya.
Tolong buatkan sistem QR dinamis: migration qr_tokens, API generate token,
halaman admin yang menampilkan QR besar dan auto-refresh setiap 30 detik 
menggunakan fetch() + countdown timer tanpa reload halaman.
```

**Hanya Scan + GPS:**
```
Saya punya project Laravel 11 dengan tabel qr_tokens dan attendances sudah ada.
Tolong buatkan halaman absensi mobile-friendly yang:
1. Scan QR menggunakan html5-qrcode (CDN)
2. Validasi token ke server via POST /absensi/scan
3. Ambil GPS browser lalu kirim ke POST /absensi/submit
4. Server validasi GPS dengan Haversine formula
5. Tampilkan hasil konfirmasi atau error
```

**Hanya Dashboard + Export:**
```
Saya punya project Laravel 11 dengan model Attendance dan User sudah ada.
Tolong buatkan:
1. Dashboard admin dengan statistik hari ini dan tabel absensi
2. Halaman rekap dengan filter tanggal dan nama guru
3. Export Excel menggunakan maatwebsite/laravel-excel
4. Export PDF menggunakan barryvdh/laravel-dompdf
```
