## Informasi Proyek

- Nama        : Sistem Pengaduan Mahasiswa
- Kelompok    : 3
- Mata kuliah : Pemrograman Web Lanjut (INF3021)

### Anggota

- Cantika Dwi Utari — NIM 25170023            [Documentasi]
- Hafizh Zaidan Muttaqien — NIM 25170006      [Frontend]
- Naufal Marzuki Fadhil — NIM 25170018        [Backend]
- Paskalino Adrianus Warorowai — NIM 25170024 [Kordinator | DATABASE]

# Sistem Pengaduan Mahasiswa

Aplikasi web untuk mahasiswa mengirim dan memantau pengaduan kampus, serta untuk
admin mengelola tindak lanjut pengaduan dan akun mahasiswa.

## Teknologi

- PHP 8.3 atau lebih baru
- Laravel 13
- Database SQLite secara default; MySQL juga didukung
- Blade dan CSS statis (`public/css`)
- Composer

Aplikasi tidak memerlukan Node.js, npm, Vite, atau proses build frontend.
Fitur lupa kata sandi mengirim tautan reset melalui email dan memerlukan
konfigurasi mail pada `.env`.

## Menjalankan Secara Lokal

Persyaratan: PHP dengan ekstensi `pdo_sqlite` dan Composer.

Dari folder proyek, jalankan:

```powershell
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser. Saat `.env` belum ada, skrip Composer
membuat konfigurasi lokal dengan SQLite dan membuat file database
`database/database.sqlite`. File konfigurasi dan database lokal tidak perlu
diunduh atau disalin dari repository. Jika `.env` sudah tersedia, Composer tidak
menimpanya; perintah migrasi akan menggunakan koneksi database yang ditentukan
di sana.

### Menggunakan MySQL (opsional)

Buat database kosong, lalu atur koneksi pada `.env`, misalnya:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pengaduan_mahasiswa
DB_USERNAME=root
DB_PASSWORD=
```

Pastikan layanan MySQL berjalan, kemudian jalankan:

```powershell
php artisan migrate --seed
```

## Akun Demo

Seeder menyediakan akun berikut untuk pengembangan lokal:

| Peran | NIM / Email | Kata sandi |
|---|---|---|
| Mahasiswa | `25170023` | `password` |
| Admin | `admin@kampus.ac.id` | `password` |

Ganti kredensial demo sebelum aplikasi digunakan di lingkungan bersama atau
produksi.

## Fitur

### Mahasiswa

- Mendaftar dan masuk menggunakan NIM atau email.
- Membuat pengaduan dengan kategori, judul, deskripsi, dan lokasi opsional.
- Melihat, mencari, dan memantau pengaduan milik sendiri.
- Mengubah atau menghapus pengaduan sendiri selama statusnya `Menunggu`.
- Mengganti foto profil (maksimal 500 KB) dan kata sandi dari halaman profil.
- Nama, NIM, dan email tidak dapat diubah dari halaman profil.

### Admin

- Melihat dan mencari seluruh pengaduan.
- Mengubah status pengaduan menjadi `Menunggu`, `Diproses`, `Selesai`, atau
  `Ditolak`; isi laporan mahasiswa tidak dapat diedit oleh admin.
- Mengelola akun mahasiswa; akun admin tidak ditampilkan dan tidak dapat
  dikelola melalui manajemen pengguna.
- Mengunduh rekap pengaduan dalam format CSV atau laporan PDF yang memuat
  penjelasan, grafik status, diagram alur, dan tabel laporan.

Daftar mahasiswa diurutkan berdasarkan NIM secara ascending. Foto baru langsung
disimpan saat formulir profil dikirim.

Endpoint JSON `GET /api/complaints` juga tersedia bagi pengguna yang sudah
masuk. Data mahasiswa dibatasi pada pengaduan miliknya, sedangkan admin dapat
melihat semua pengaduan. Pencarian mendukung judul, deskripsi, kategori, dan
nama pelapor.

### Autentikasi dan Session

Setelah login, mahasiswa diarahkan ke dashboard mahasiswa (`/user`) dan admin
ke dashboard admin (`/admin`). Jika URL dashboard role lain tersimpan sebagai
tujuan redirect di session, tujuan tersebut diabaikan dan pengguna diarahkan ke
dashboard sesuai rolenya. Redirect ke halaman lain yang diminta sebelum login
tetap dipertahankan.

## Audit Backend

Pemeriksaan lokal terakhir tidak menemukan fatal error yang dapat direproduksi:
86 tes lulus dengan 445 assertions, sintaks 37 file PHP valid, `route:list`
berhasil, seluruh 8 migrasi tercatat sudah berjalan pada database SQLite aktif,
dan `composer validate --no-check-publish` berhasil. Pemeriksaan migrasi ini
hanya mencakup database yang sedang dikonfigurasi; koneksi MySQL produksi tidak
diuji.

Catatan kapasitas: ekspor CSV membaca data secara bertahap, tetapi ekspor PDF
saat ini memuat seluruh pengaduan sekaligus ke memori untuk dirender. Jika jumlah
laporan bertambah besar, unduhan PDF dapat menjadi lambat atau membutuhkan
memori lebih besar. Ini bukan kegagalan yang muncul pada tes saat ini.

## Pengujian

Jalankan seluruh tes dengan:

```powershell
php artisan test
```

## Struktur Proyek

- `app/` — model, controller, policy, dan logika aplikasi.
- `database/migrations/` — skema database.
- `database/seeders/` — kategori dan data demo.
- `resources/views/` — halaman Blade.
- `public/css/` — stylesheet statis.
- `routes/web.php` — rute halaman dan endpoint JSON.
- `tests/` — tes fitur dan unit.
- `tests/Feature/AuthFlowTest.php` — tes autentikasi dan redirect dashboard
  sesuai role.
- `docs/README.md` — penjelasan detail struktur dan alur backend.
- `docs/ERD.md` — ERD, kamus data, dan matriks hak akses.
