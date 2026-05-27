# 📋 MKAS Laravel - Project Summary & Documentation

Aplikasi **MKAS Laravel** adalah platform sistem manajemen keuangan kas, iuran (deposits), transaksi pengeluaran/pemasukan, dan perencanaan pembayaran (payment plans). Aplikasi ini memiliki sistem otorisasi berbasis peran (Admin dan User) serta dilengkapi dengan integrasi penyimpanan Google Drive pihak ketiga untuk memuat bukti transaksi dan avatar.

---

## 🌟 Fitur Utama (Core Features)

1. **Manajemen Pengguna (User Management)**:
   - Registrasi dan otentikasi pengguna berbasis peran (Admin dan User).
   - Pengaturan profil lengkap, termasuk unggah avatar yang terintegrasi dengan Google Drive.
   - Form ubah kata sandi opsional dengan pengamanan enkripsi Bcrypt.

2. **Manajemen Iuran (Deposits)**:
   - Pencatatan iuran wajib bulanan pengguna dengan status verifikasi (`PENDING`, `APPROVED`, `REJECTED`).
   - Fitur pencarian keyword dan filter kelompok dinamis berdasarkan Bulan/Tahun Iuran, Status, dan Pembuat Deposit.

3. **Manajemen Transaksi (Transactions)**:
   - Pencatatan transaksi masuk (`IN`) dan keluar (`OUT`) berdasarkan kategori tertentu.
   - Verifikasi transaksi oleh Admin beserta catatan (admin note) dan bukti respons (admin photo).
   - Fitur pencarian keyword dan filter kelompok dinamis berdasarkan Rentang Tanggal, Tipe Transaksi, Status, Kategori, dan Pembuat Transaksi.

4. **Rencana Pembayaran (Payment Plans)**:
   - Pengajuan anggaran atau rencana pengeluaran masa depan oleh pengguna.
   - Konversi langsung rencana pembayaran yang telah disetujui (`APPROVED`) menjadi transaksi pengeluaran riil.

5. **Integrasi Google Drive (`gdrive-service`)**:
   - Memuat gambar bukti transaksi dan foto profil secara aman dari container eksternal `gdrive-service` menggunakan **Google Drive Proxy Controller** di Laravel. Hal ini menghindari eksposur kredensial Google API langsung di sisi client.

6. **Optimasi Antarmuka Pengguna (UI/UX)**:
   - **Skeleton Loader (Shimmer Effect)**: Efek pemuatan visual transisi saat gambar bukti transaksi dimuat dari Google Drive.
   - **Full Image Preview (Lightbox)**: Popup tampilan gambar penuh untuk melihat detail bukti transaksi atau respons admin dengan latar belakang overlay gelap.
   - **Smart Action Dropdown**: Menu aksi titik tiga otomatis mendeteksi baris terbawah pada tabel dan membuka menu ke arah atas (`bottom: 100%`) agar tidak terpotong oleh overflow batas tabel.

---

## 🏗️ Arsitektur Sistem & Aliran Data

Aplikasi berjalan di atas arsitektur kontainer Docker dengan konfigurasi multi-service:
- **`wkas-app`**: Kontainer aplikasi Laravel 11 utama (PHP 8.4-cli-alpine) yang melayani request web pada port `8090`.
- **`gdrive-service`**: Kontainer API eksternal (port `8000` internal, `8095` eksternal) yang menangani komunikasi langsung ke Google Drive API.
- **`mkas-mysql`**: Kontainer database MySQL.

### Aliran Proxy Gambar Google Drive:
```
[Client/Browser] 
       │
       ▼ (Request Route: /backoffice/gdrive/preview?path=...)
[Laravel Controller (GoogleDriveProxyController)]
       │
       ▼ (HTTP Request Internal ke http://gdrive-service:8000/api/preview)
[gdrive-service Container]
       │
       ▼ (API Call dengan Service Account)
[Google Drive API]
```

---

## 🗄️ Desain Basis Data (Database Design)

Struktur tabel diatur secara relasional dengan tabel-tabel utama berikut (Detail ERD dapat dilihat di berkas [`ERD.md`](file:///\\wsl.localhost/Ubuntu/home/rijal/projects/laravel/wkas-laravel/ERD.md)):

* **`users`**: Menyimpan data akun pengguna, peran (`role`), dan foto profil (`photo`).
* **`categories`**: Menyimpan daftar kategori transaksi (misal: Konsumsi, Inventaris, Kas).
* **`payment_accounts`**: Akun rekening bank penerima iuran dan nominal iuran bulanan wajib (`monthly_amount`).
* **`deposits`**: Transaksi iuran masuk dari anggota ke rekening bank dengan referensi bulan (`month`) dalam format `YYYY-MM`.
* **`transactions`**: Transaksi keuangan masuk (`IN`) atau keluar (`OUT`) yang terikat dengan kategori dan pembuat.
* **`payment_plans`**: Pengajuan rencana anggaran belanja sebelum dieksekusi menjadi transaksi pengeluaran.

---

## 🚀 Integrasi CI/CD & Deployment

Aplikasi ini dilengkapi dengan alur kerja **CI/CD otomatis** menggunakan **GitHub Actions** (`.github/workflows/deploy.yml`):

1. **Multi-stage Docker Build**:
   - **Stage 1 (Frontend Builder)**: Menggunakan Node.js 20 untuk mengompilasi assets frontend lewat Vite (`npm run build`).
   - **Stage 2 (Composer Builder)**: Menggunakan Composer 2.8 untuk mengunduh packages dependency PHP secara bersih tanpa development dependency (`composer install --no-dev`).
   - **Stage 3 (Production Stage)**: Menyalin hasil kompilasi dan dependency PHP ke runtime image `php:8.4-cli-alpine` tanpa menyertakan node_modules, git, atau composer untuk efisiensi performa dan keamanan optimal.
2. **Docker Hub Registration**:
   - Image yang berhasil dibangun otomatis didorong (pushed) ke repositori **Docker Hub** dengan tag `latest` dan tag SHA commit.
3. **Automated Deploy via SSH**:
   - GitHub Actions akan masuk secara aman via SSH ke server VPS target, menarik image terbaru (`docker compose pull`), menyalakan ulang kontainer (`docker compose up -d`), dan menjalankan optimasi Laravel serta migrasi database secara otomatis:
     - `php artisan migrate --force`
     - `php artisan config:cache`
     - `php artisan route:cache`
     - `php artisan view:cache`

---

## ⚙️ Persyaratan Menjalankan Aplikasi

### Konfigurasi `.env` Utama:
```ini
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...

DB_CONNECTION=mysql
DB_HOST=mkas-mysql
DB_PORT=3306
DB_DATABASE=mkas_db
DB_USERNAME=root
DB_PASSWORD=root

GDRIVE_SERVICE_URL=http://gdrive-service:8000
```
Untuk petunjuk konfigurasi CI/CD GitHub Secrets dan deploy server lebih rinci, silakan lihat file panduan [walkthrough.md](file:///C:/Users/Jerry/.gemini/antigravity/brain/fd75ccbf-13a3-4930-a667-a4e0186b29b3/walkthrough.md).
