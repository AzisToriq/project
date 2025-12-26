# Project Management System (AzisToriq/project)

Proyek ini adalah sistem manajemen berbasis web yang dibangun menggunakan **Laravel**. Aplikasi ini mendukung multi-role login untuk Admin, Siswa, dan Orang Tua.

## 🚀 Fitur Utama
- **Multi-Role Login**: Akses berbeda untuk Admin, Siswa, dan Orang Tua.
- **Database Seeding**: Data dummy siap pakai untuk pengujian.
- **Manajemen Data**: Pengelolaan informasi sekolah/project secara terintegrasi.

---

## 🛠 Panduan Instalasi (Step-by-Step)

Pastikan kamu sudah menginstal **PHP (>= 8.x)**, **Composer**, dan **MySQL/MariaDB** (bisa menggunakan XAMPP atau Laragon).

### 1. Clone Repositori
Buka terminal atau CMD, lalu jalankan perintah berikut:
```bash
git clone [https://github.com/AzisToriq/project.git](https://github.com/AzisToriq/project.git)
cd project

```

### 2. Instal Dependency

Instal semua library PHP yang dibutuhkan melalui Composer:

```bash
composer install

```

### 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env

```

*Jika di Windows CMD:* `copy .env.example .env`

**PENTING:** Buka file `.env` dan sesuaikan pengaturan database kamu:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_kamu  # Ganti dengan nama database yang kamu buat
DB_USERNAME=root
DB_PASSWORD=

```

### 4. Generate App Key

```bash
php artisan key:generate

```

### 5. Migrasi & Seeder (Sangat Penting)

Langkah ini akan membuat tabel dan mengisi data login otomatis (Siswa & Ortu) agar kamu tidak perlu daftar manual:

```bash
php artisan migrate --seed

```

### 6. Jalankan Server

```bash
php artisan serve

```

Aplikasi sekarang bisa diakses di: [http://127.0.0.1:8000](https://www.google.com/search?q=http://127.0.0.1:8000)

---

## 🔐 Informasi Login (Akses Demo)

Gunakan akun berikut untuk mencoba fitur berdasarkan role masing-masing:

### A. Login Siswa

* **URL**: `http://127.0.0.1:8000/siswa/login`
* **Username/Email**: (Cek di UserSeeder atau gunakan email siswa yang terdaftar)
* **Password**: `password123`

### B. Login Orang Tua (Ortu)

* **URL**: `http://127.0.0.1:8000/ortu/login`
* **Username/Email**: (Cek di UserSeeder atau gunakan email ortu yang terdaftar)
* **Password**: `orangtua123`

### C. Login Admin/Umum

* **URL**: `http://127.0.0.1:8000/login`
* **Password**: (Biasanya menggunakan default `password`)

---

## 📂 Struktur Folder Penting

* `app/Models`: Lokasi file model database.
* `database/seeders`: Lokasi data login (Jika ingin mengubah password/user).
* `routes/web.php`: Lokasi pengaturan URL (Siswa, Ortu, Admin).

## 🆘 Troubleshooting

* **Gagal Migrasi?** Pastikan database sudah dibuat di phpMyAdmin dengan nama yang sama seperti di file `.env`.
* **Error 404 pada Login?** Pastikan kamu mengakses sub-folder yang benar (`/siswa/login` atau `/ortu/login`).
* **Peringatan CRLF?** Itu hanya masalah format baris Windows, tidak akan merusak aplikasi.

---

Dibuat dengan oleh [Azis Toriq](https://www.google.com/search?q=https://github.com/AzisToriq)

```

***

### Catatan Tambahan untuk Kamu:
1. **Database**: Pastikan kamu sudah membuat database kosong di phpMyAdmin sebelum menjalankan `php artisan migrate --seed`.
2. **Email Seeder**: Di bagian README "Informasi Login", kalau kamu hafal email yang kamu buat di file Seeder (misal: `siswa@gmail.com`), sebaiknya langsung tuliskan di sana agar orang yang meng-clone tidak perlu buka file code lagi.
3. **Link Github**: Pastikan repositori kamu sudah bersifat **Public** agar orang lain bisa melakukan `git clone`.

```
