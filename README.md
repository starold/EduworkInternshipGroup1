# 🏍️ ED.RENT - Sistem Informasi Rental Sepeda Motor

**ED.RENT** adalah platform aplikasi web rental sepeda motor berbasis **Laravel** yang mengusung arsitektur **MVC (Model-View-Controller)**. Aplikasi ini dirancang untuk memudahkan proses penyewaan kendaraan roda dua secara fleksibel, transparan, dan terstruktur, dengan memisahkan hak akses antara **Penyewa (Customer)** dan **Pengelola (Admin)**.

---

## 📑 Daftar Isi
1. [Fitur Utama](#-fitur-utama)
2. [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
3. [Struktur Folder & Penjelasan Lengkap](#-struktur-folder--penjelasan-lengkap)
4. [Alur Framework Laravel (MVC Request Lifecycle)](#-alur-framework-laravel-mvc-request-lifecycle)
5. [Alur Bisnis Aplikasi (Business & User Flow)](#-alur-bisnis-aplikasi-business--user-flow)
6. [Skema & Relasi Database (ERD)](#-skema--relasi-database-erd)
7. [Tata Cara Menjalankan Aplikasi (Setup & Running)](#-tata-cara-menjalankan-aplikasi-setup--running)
8. [Akun Pengguna Default](#-akun-pengguna-default)
9. [Perintah Artisan Penting (Cheat Sheet)](#-perintah-artisan-penting-cheat-sheet)

---

## 🌟 Fitur Utama

### 👤 Sisi Penyewa (Customer)
- **Dialog Profil Pengguna**: Modal edit data diri (Nama Lengkap, No WhatsApp, No. KTP, No. SIM, dan Avatar/Foto Profil) dapat diakses dari navbar di seluruh halaman (Home, Deskripsi Kendaraan, Daftar Pesanan).
- **Katalog & Deskripsi Kendaraan**: Tampilan detail spesifikasi motor, perlengkapan sewa (Helm SNI, Bensin, STNK, Sarung Tangan), modal interaktif **"Pilih Durasi Sewa"** (1-5 hari) dengan kalkulasi otomatis tanggal selesai, serta daftar rekomendasi kendaraan lainnya.
- **Pemesanan Fleksibel (Booking)**: Menentukan tanggal mulai & tanggal selesai sewa, metode pengambilan (Ambil di Rental / Diantar ke Lokasi), serta kalkulasi biaya harian dan deposit jaminan secara otomatis.
- **Waktu Pembayaran & Deadline Interaktif**: Countdown timer real-time `HHj : MMm : SSd` batas waktu pembayaran pada halaman pembayaran dan detail pesanan, lengkap dengan halaman status **Pembayaran Berhasil** dan **Pembayaran Gagal (Waktu Habis)**.
- **Deadline Pemakaian Motor**: Informasi batas waktu penggunaan motor (tanggal dan jam selesai sewa) ditampilkan jelas pada popup Detail Pemesanan dan halaman detail pesanan.
- **Daftar Pesanan & Status Tracking**: Memantau status pesanan sewa secara berkala (*Menunggu Pembayaran*, *Menunggu Konfirmasi*, *Dikonfirmasi*, *Sedang Berjalan*, *Selesai*, *Dibatalkan*).
- **Autentikasi Aman**: Login, Registrasi, dan manajemen sesi pengguna.

### 🛡️ Sisi Pengelola (Admin Panel)
- **Dialog Profil Admin**: Modal edit data profil yang dapat diakses langsung dari topbar seluruh halaman admin.
- **Dashboard Ringkasan Operasional**: Statistik total unit motor, pesanan yang butuh konfirmasi, dan sewa aktif.
- **Manajemen Armada Motor (Modal CRUD)**: Menambah atau mengedit unit motor melalui modal interaktif **"Detail Kendaraan"** (upload foto, deskripsi, merk, model, plat nomor, harga harian, kapasitas tangki, status) serta menghapus unit.
- **Update Pesanan Cepat (Modal)**: Memperbarui status pesanan sewa melalui modal **"Update Pemesanan"** yang menampilkan info pemesan, tanggal mulai sewa, dan **deadline pemakaian motor**.

---

## 🛠️ Teknologi yang Digunakan
- **Backend Framework**: Laravel 13
- **Bahasa Pemrograman**: PHP 8.5
- **Database Engine**: MySQL / MariaDB (XAMPP)
- **ORM**: Eloquent ORM
- **Frontend / View Engine**: Laravel Blade Templating & Vanilla CSS modern (responsif, tanpa dependensi framework CSS berat)
- **Dependency Manager**: Composer 2.x

---

## 📂 Struktur Folder & Penjelasan Lengkap

Berikut adalah pemetaan folder dan berkas penting di dalam proyek:

```text
EduworkInternshipGroup1/
├── app/                  # Inti logika aplikasi (Models, Controllers, Middleware, Enums)
│   ├── Enums/            # Definisi status & role bertipe aman (PHP 8.1+ Enums)
│   ├── Http/
│   │   ├── Controllers/  # Penangan request dari route (Auth, Customer, Admin)
│   │   └── Middleware/   # Filter & penjaga otorisasi route (AdminMiddleware)
│   └── Models/           # Representasi tabel database (Eloquent ORM)
├── bootstrap/            # Berkas inisialisasi awal saat framework booting
├── config/               # Seluruh konfigurasi global aplikasi (database, auth, session, dll)
├── database/             # Skema tabel, seeder, dan factory database
│   ├── factories/        # Generator data dummy untuk testing
│   ├── migrations/       # Blueprint skema tabel database (DDL)
│   └── seeders/          # Pengisi data awal ke database (DatabaseSeeder)
├── public/               # Titik akses web publik (index.php, CSS, JS, symlink storage)
├── resources/            # Kode tampilan dan aset mentah
│   ├── css/ & js/        # Skrip dan stylesheet frontend
│   └── views/            # Template tampilan HTML Blade (.blade.php)
├── routes/               # Definisi alamat URL dan endpoint aplikasi (web.php)
├── storage/              # Penyimpanan internal (log, cache session, file upload publik)
├── tests/                # Pengujian otomatis (Pest / Feature & Unit Test)
├── vendor/               # Library pihak ketiga yang diinstal via Composer
├── .env                  # Pengaturan variabel lingkungan (koneksi DB, APP_KEY)
├── artisan               # Runner perintah baris perintah Laravel
└── composer.json         # Konfigurasi dependensi paket PHP
```

### Penjelasan Rinci Sub-Folder Utama:

1. **`app/Enums/`**:
   - `UserRole.php`: Mengatur hak akses peran pengguna (`Admin`, `Customer`).
   - `MotorStatus.php`: Status ketersediaan motor (`Available`, `Rented`, `Maintenance`).
   - `BookingStatus.php`: Alur status transaksi (`Pending`, `Confirmed`, `Ongoing`, `Completed`, `Cancelled`).
   - `PaymentStatus.php`: Status verifikasi pembayaran (`Unpaid`, `PendingConfirmation`, `Paid`, `Failed`).
   - `PickupMethod.php`: Opsi pengambilan motor (`SelfPickup`, `Delivery`).

2. **`app/Http/Controllers/`**:
   - `Auth/`: Menangani autentikasi pengguna (`LoginController.php` & `RegisterController.php`).
   - `Customer/`: Logika untuk penyewa (`HomeController.php`, `BookingController.php`, `PaymentController.php`).
   - `Admin/`: Logika panel pengelola (`DashboardController.php`, `MotorController.php`, `BookingController.php`).

3. **`app/Http/Middleware/`**:
   - `AdminMiddleware.php`: Memastikan pengguna telah login dan berstatus `admin` sebelum mengakses URL grup `/admin/*`.

4. **`app/Models/`**:
   - `User.php`: Model data pengguna (admin & customer).
   - `MotorCategory.php`: Kategori motor (Matic, Manual, Sport, Trail).
   - `Motor.php`: Data unit kendaraan dan relasi ke kategori dan pemesanan.
   - `Booking.php`: Data induk transaksi pemesanan.
   - `BookingItem.php`: Rincian unit motor yang disewa dalam satu pesanan.
   - `Payment.php`: Data transaksi dan berkas bukti pembayaran.
   - `Rental.php`: Pencatatan operasional sewa saat motor sedang dipakai.
   - `Review.php` & `Fine.php`: Ulasan penyewa dan denda keterlambatan/kerusakan.

5. **`resources/views/`**:
   - `layouts/`: Master template halaman (`app.blade.php` untuk customer, `admin.blade.php` untuk admin, `guest.blade.php` untuk auth).
   - `customer/`: Tampilan halaman beranda, katalog kendaraan, pembuatan booking, detail booking, dan form upload pembayaran.
   - `admin/`: Tampilan dashboard admin, kelola motor (list, create, edit), dan verifikasi booking.

---

## 🔄 Alur Framework Laravel (MVC Request Lifecycle)

Bagaimana sebuah permintaan diproses dari browser hingga menghasilkan halaman web:

```text
 ┌──────────────────┐
 │ Browser Pengguna │ ──(1) Kirim HTTP Request (contoh: GET /kendaraan)
 └────────┬─────────┘
          │
          ▼
 ┌──────────────────┐
 │ public/index.php │ ──(2) Booting Core Framework & Kernel
 └────────┬─────────┘
          │
          ▼
 ┌──────────────────┐
 │  routes/web.php  │ ──(3) Cocokkan URL dengan Route yang terdaftar
 └────────┬─────────┘
          │
          ▼
 ┌──────────────────┐
 │    Middleware    │ ──(4) Validasi Hak Akses (Auth, Admin Check, CSRF)
 └────────┬─────────┘
          │
          ▼
 ┌──────────────────┐
 │    Controller    │ ──(5) Eksekusi Logika Bisnis & Pengambilan Data
 └────────┬─────────┘
          │
     ┌────┴───────────────────────────┐
     ▼                                ▼
 ┌────────────────────────┐      ┌────────────────────────┐
 │   Model (Eloquent)     │      │   Blade View Engine    │
 └────────┬───────────────┘      └────────┬───────────────┘
          │                               │
          ▼ (Query ke DB)                 ▼ (Kompilasi HTML)
 ┌────────────────────────┐      ┌────────────────────────┐
 │     Database MySQL     │      │   HTML Web Response    │
 └────────────────────────┘      └────────┬───────────────┘
                                          │
                                          ▼
                                 ┌──────────────────┐
                                 │ Browser Pengguna │
                                 └──────────────────┘
```

1. **Request Diterima**: Pengguna mengakses URL melalui peramban web.
2. **Entry Point (`public/index.php`)**: Memuat dependensi Composer dan memulai aplikasi Laravel.
3. **Pencocokan Rute (`routes/web.php`)**: Laravel mencari controller dan fungsi yang sesuai dengan rute.
4. **Pemeriksaan Middleware**: Rute yang dilindungi (seperti halaman admin atau form sewa) diperiksa terlebih dahulu keabsahan sesi loginnya.
5. **Eksekusi Controller**: Controller memproses parameter input, berinteraksi dengan **Model Eloquent** untuk menarik/mengubah data di database MySQL.
6. **Rendering View**: Controller mengoper data hasil kueri ke template Blade (`.blade.php`).
7. **Pengiriman Response**: Template dikompilasi menjadi dokumen HTML murni dan dikembalikan ke browser dengan kode status HTTP 200 OK.

---

## 💼 Alur Bisnis Aplikasi (Business & User Flow)

### 🛵 A. Alur Penyewa (Customer Flow)
1. **Eksplorasi Motor**: Customer mengunjungi halaman utama atau menu **Pilih Kendaraan** untuk melihat daftar motor yang tersedia beserta tarif harian dan spesifikasinya.
2. **Login / Daftar**: Jika belum memiliki akun, customer dapat mendaftar di menu **Daftar** lalu login.
3. **Reservasi Unit**: Customer memilih motor, menentukan tanggal sewa dan tanggal kembali, serta memilih metode pengambilan (*Ambil di Garasi* atau *Diantar ke Lokasi*).
4. **Kalkulasi Biaya Otomatis**: Sistem menghitung total hari sewa × harga per hari + biaya deposit jaminan.
5. **Pembayaran**: Customer diarahkan ke halaman pembayaran, mentransfer sesuai nominal yang tertera, lalu mengunggah foto struk/bukti transfer.
6. **Pemantauan Status**: Customer memantau proses verifikasi pesanan pada menu **Daftar Pesanan**.

### 🛠️ B. Alur Pengelola (Admin Flow)
1. **Login Admin**: Masuk melalui `/login` dengan kredensial administrator.
2. **Dashboard Monitoring**: Mengamati ringkasan total armada, jumlah pesanan baru, dan status kendaraan.
3. **Kelola Armada Motor**: Admin mendaftarkan motor baru (merk, model, tahun, plat nomor, tarif sewa harian, foto unit) atau memperbarui status motor yang sedang diservis.
4. **Verifikasi Pesanan**: Membuka menu pesanan masuk, memeriksa keaslian bukti transfer yang diunggah customer, lalu mengklik tombol **Konfirmasi** atau **Tolak**.

---

## 🗄️ Skema & Relasi Database (ERD)

Aplikasi menggunakan skema database relasional ternormalisasi:

- **`users`**: Data identitas pengguna dan peran (`role`: `admin`, `customer`).
- **`motor_categories`**: Kategori kendaraan (Matic, Manual, Sport, Trail).
  - *Relasi*: Satu kategori memiliki banyak motor (*One-to-Many* ke `motors`).
- **`motors`**: Data unit sepeda motor (merk, model, warna, nomor plat, harga sewa per hari, deposit, deskripsi, foto, status).
- **`bookings`**: Data transaksi penyewaan (kode booking unik, tanggal mulai, tanggal selesai, metode pengambilan, status pesanan).
  - *Relasi*: Dimiliki oleh satu user (*Many-to-One* ke `users`).
- **`booking_items`**: Rincian motor yang disewa per pesanan (harga sewa, subtotal hari, durasi).
- **`payments`**: Data pembayaran dan berkas bukti transfer (*One-to-One* dengan `bookings`).
- **`rentals`** & **`rental_returns`**: Log operasional serah-terima fisik motor dan pengembalian unit.
- **`reviews`** & **`fines`**: Ulasan kepuasan pelanggan serta pencatatan denda (keterlambatan/kerusakan).

---

## ⚡ Tata Cara Menjalankan Aplikasi (Setup & Running)

### 1. Prasyarat Sistem
Pastikan di komputer Anda sudah terpasang:
- **XAMPP** (dengan modul Apache & MySQL)
- **PHP** (minimal versi 8.3 atau 8.5)
- **Composer** (versi 2.x)

---

### 2. Nyalakan Database di XAMPP
1. Buka aplikasi **XAMPP Control Panel**.
2. Klik tombol **Start** pada baris **MySQL** (dan baris **Apache**).
3. Pastikan indikator **MySQL** berwarna **hijau** (aktif di port default 3306).

---

### 3. Langkah Menjalankan Aplikasi (Running Daily)

Buka terminal pada folder proyek (`EduworkInternshipGroup1`), lalu jalankan server Laravel:

```powershell
php artisan serve
```

Jika server berhasil berjalan, terminal akan menampilkan:
```text
INFO  Server running on [http://127.0.0.1:8000].
Press Ctrl+C to stop the server
```

> 💡 **Troubleshooting: Jika muncul error *"php : The term 'php' is not recognized"***
> Muat path binary PHP terlebih dahulu di jendela terminal yang sama dengan mengetik:
> ```powershell
> $env:Path = "C:\Users\Hp\.config\herd-lite\bin;" + $env:Path
> php artisan serve
> ```

---

### 4. Akses Web Melalui Browser
Buka browser dan buka tautan berikut:
- **Halaman Depan / Publik**: [http://localhost:8000](http://localhost:8000)
- **Daftar Seluruh Kendaraan**: [http://localhost:8000/kendaraan](http://localhost:8000/kendaraan)
- **Halaman Login**: [http://localhost:8000/login](http://localhost:8000/login)
- **Halaman Pendaftaran**: [http://localhost:8000/register](http://localhost:8000/register)
- **Dashboard Admin**: [http://localhost:8000/admin/dashboard](http://localhost:8000/admin/dashboard)

---

### 5. Cara Mematikan Server
- Klik pada jendela terminal tempat server berjalan, lalu tekan kombinasi tombol keyboard **`Ctrl` + `C`**.
- Pada XAMPP Control Panel, Anda dapat mengklik tombol **Stop** pada modul **MySQL**.

---

## 🔑 Akun Pengguna Default

Database telah dilengkapi data awal (*seeder*) yang siap digunakan untuk pengujian:

| Peran (Role) | Email | Password | Fungsi Akun |
|---|---|---|---|
| **Administrator** | `admin@edrent.com` | `password` | Mengakses dashboard admin, mengelola armada motor, dan memverifikasi pesanan sewa. |
| **Penyewa (Customer)** | `budi@example.com` | `password` | Menguji alur pencarian motor, membuat booking, dan mengunggah bukti bayar. |

*(Anda juga dapat membuat akun customer baru secara mandiri melalui menu Register).*

---

## 📌 Perintah Artisan Penting (Cheat Sheet)

Kumpulan perintah bermanfaat saat pengembangan aplikasi:

```powershell
# Menjalankan server aplikasi
php artisan serve

# Menjalankan migrasi database
php artisan migrate

# Me-reset database dan mengisi ulang data dummy (Fresh + Seed)
php artisan migrate:fresh --seed

# Membuat symlink agar file upload dapat diakses publik
php artisan storage:link

# Melihat seluruh daftar rute URL aplikasi
php artisan route:list

# Membersihkan cache konfigurasi dan rute aplikasi
php artisan optimize:clear
```
