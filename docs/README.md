# Panduan Backend

Dokumen ini menjelaskan struktur dan alur backend aplikasi Sistem Pengaduan Mahasiswa yang dibangun menggunakan Laravel 13 dan PHP 8.3 atau lebih baru.

## Daftar Isi

- [Ringkasan Backend](#ringkasan-backend)
- [Struktur Folder](#struktur-folder)
- [Alur Request](#alur-request)
- [Route dan Endpoint](#route-dan-endpoint)
- [Controller](#controller)
- [Model dan Relasi](#model-dan-relasi)
- [Database](#database)
- [Autentikasi dan Hak Akses](#autentikasi-dan-hak-akses)
- [Validasi dan Respons](#validasi-dan-respons)
- [Menjalankan dan Menguji Backend](#menjalankan-dan-menguji-backend)
- [Troubleshooting](#troubleshooting)

## Ringkasan Backend

Backend menerima request HTTP, mencocokkan URL dengan route, menjalankan controller, mengakses database melalui model Eloquent, lalu mengirim respons berupa halaman Blade, redirect, file CSV, atau JSON.

Backend utama berada di `app/`, `routes/`, `database/`, `config/`, dan `bootstrap/`. Folder `resources/views/` memuat halaman Blade yang ditampilkan oleh controller.

## Struktur Folder

| Lokasi | Fungsi |
|---|---|
| `app/Http/Controllers/` | Logika request untuk autentikasi, pengaduan, dashboard, profil, pengguna, kata sandi, dan ekspor laporan. |
| `app/Http/Controllers/Api/` | Controller endpoint JSON. |
| `app/Http/Resources/` | Mengubah model menjadi bentuk respons JSON API. |
| `app/Models/` | Model Eloquent dan relasi database: `User`, `Complaint`, dan `Category`. |
| `app/Policies/` | Aturan otorisasi resource, termasuk akses ke pengaduan. |
| `app/Providers/` | Registrasi layanan dan konfigurasi aplikasi. |
| `routes/web.php` | Route halaman web dan endpoint JSON yang saat ini didaftarkan aplikasi. |
| `routes/console.php` | Perintah Artisan berbasis closure. |
| `database/migrations/` | Riwayat dan definisi perubahan skema tabel. |
| `database/seeders/` | Data awal dan data demo. |
| `database/factories/` | Pembuatan data contoh untuk pengujian. |
| `config/` | Konfigurasi aplikasi, autentikasi, database, session, mail, storage, dan layanan lain. |
| `bootstrap/app.php` | Pendaftaran routing, middleware, dan perilaku penanganan exception. |
| `tests/Feature/` dan `tests/Unit/` | Tes alur HTTP dan tes unit. |

## Alur Request

Alur request umumnya:

1. Browser atau client mengirim request ke aplikasi.
2. Laravel mencocokkan method dan URL dengan route di `routes/web.php`.
3. Middleware route memeriksa kondisi seperti status login.
4. Controller memvalidasi input, menjalankan aturan akses, dan memanggil model.
5. Model Eloquent membaca atau mengubah database.
6. Controller mengembalikan view, redirect, unduhan CSV, atau API Resource JSON.

Contoh membuat pengaduan:

`POST /complaints` → `ComplaintController::store()` → validasi field dan kategori aktif → simpan melalui relasi user yang sedang login → redirect ke detail pengaduan.

## Route dan Endpoint

Route web aplikasi didefinisikan di `routes/web.php`. Route autentikasi tertentu memakai middleware `guest`, sedangkan dashboard, profil, pengaduan, pengguna, API pengaduan, dan logout berada di dalam grup middleware `auth`.

| Method dan URL | Route name | Fungsi |
|---|---|---|
| `GET /login` | `login` | Form login. |
| `POST /login` | `login.store` | Autentikasi dengan NIM atau email dan kata sandi. |
| `GET /register` | `register` | Form pendaftaran mahasiswa. |
| `POST /register` | `register.store` | Membuat akun mahasiswa. |
| `GET /forgot-password` | `password.request` | Form permintaan tautan reset kata sandi. |
| `POST /forgot-password` | `password.email` | Mengirim tautan reset; dibatasi 5 request per menit. |
| `GET /reset-password/{token}` | `password.reset` | Form reset kata sandi. |
| `POST /reset-password` | `password.update` | Memproses reset kata sandi; dibatasi 5 request per menit. |
| `GET /user`, `GET /admin` | `dashboard`, `admin.dashboard` | Ringkasan sesuai peran. |
| `GET /user/profile`, `PUT /user/profile` | `profile.edit`, `profile.update` | Profil mahasiswa; nama, NIM, dan email hanya-baca, foto dapat diganti (maks. 500 KB). |
| `GET /admin/profile`, `PUT /admin/profile` | `admin.profile.edit`, `admin.profile.update` | Profil admin; identitas hanya-baca dan foto dapat diganti (maks. 500 KB). |
| `GET /user/profile/photo`, `GET /admin/profile/photo` | `profile.photo`, `admin.profile.photo` | Mengirim foto profil untuk pengguna yang sedang login. |
| `PUT /profile/password` | `profile.password.update` | Mengubah kata sandi dari halaman profil untuk kedua peran. |
| `GET /user/dashboard/password` | `dashboard.password.edit` | Alias kompatibilitas yang mengarahkan ke halaman profil. |
| Resource `/user/complaints` | `complaints.*` | CRUD pengaduan mahasiswa; hanya laporan sendiri yang dapat diakses dan laporan berstatus `Menunggu` yang dapat diubah/dihapus. |
| Resource `/admin/complaints` | `admin.complaints.*` | Admin dapat melihat/menghapus laporan; pembuatan dan pengeditan isi tidak diizinkan. |
| `PATCH /admin/complaints/{complaint}/status` | `admin.complaints.update-status` | Mengubah status laporan khusus admin; admin tidak dapat mengedit isi laporan. |
| `GET /admin/complaints/export` | `admin.complaints.export` | Mengunduh rekap pengaduan CSV khusus admin. |
| `GET /admin/complaints/export/pdf` | `admin.complaints.export-pdf` | Mengunduh laporan PDF dengan penjelasan, grafik, diagram, dan tabel. |
| `/admin/users` dan `/admin/users/{user}...` | `admin.users.*` | Daftar dan pengelolaan akun mahasiswa; akun admin dikecualikan. |
| `GET /api/complaints` | `api.complaints.index` | Satu endpoint JSON pengaduan: mahasiswa melihat laporan sendiri, admin melihat seluruh laporan. |
| `POST /logout` | `logout` | Mengakhiri sesi login. |

Route API saat ini berada di `routes/web.php`, bukan file route API terpisah. Endpoint tunggal `GET /api/complaints` berada di grup `auth`, sehingga memerlukan sesi login Laravel dan tidak menyediakan akses anonim. Dengan akun demo mahasiswa (`25170023` / `cantika@kampus.ac.id`), endpoint hanya mengembalikan pengaduan milik akun tersebut. Dengan akun demo admin (`admin@kampus.ac.id`), endpoint mengembalikan pengaduan dari semua pengguna. Format JSON ditangani `ComplaintResource` dan pagination Laravel. Parameter `search` mendukung pencarian judul, deskripsi, kategori, dan nama pelapor, sedangkan `page` digunakan untuk navigasi halaman.

Beberapa endpoint lama tetap tersedia sebagai alias kompatibilitas di `/complaints` dan `/users`; operasi tetap menjalankan pemeriksaan hak akses controller/policy.

## Controller

| Controller | Tanggung jawab |
|---|---|
| `AuthController` | Registrasi, autentikasi NIM/email, pembatasan percobaan login, regenerasi sesi, dan logout. |
| `PasswordController` | Permintaan/reset kata sandi melalui broker Laravel serta ganti kata sandi akun yang sudah login. |
| `DashboardController` | Menghitung ringkasan status dan mengambil lima pengaduan terbaru. Data mahasiswa dibatasi ke laporan miliknya. |
| `ComplaintController` | CRUD pengaduan, pencarian, pagination, validasi input, dan pemeriksaan otorisasi. |
| `ComplaintReportController` | Ekspor CSV bertahap dan PDF untuk admin; nilai CSV diamankan dari formula spreadsheet. |
| `ProfileController` | Menampilkan profil, hanya memperbarui foto maksimal 500 KB, dan mengirim foto untuk pengguna yang login. |
| `UserController` | Pencarian, pembaruan, dan penghapusan akun mahasiswa; daftar diurutkan berdasarkan NIM dan akun admin dikecualikan. |
| `Api/ComplaintController` | Menghasilkan daftar pengaduan JSON dengan pencarian, pagination, dan pembatasan data berdasarkan peran. |

Controller umumnya menampilkan view dari `resources/views/` atau mengembalikan respons HTTP secara langsung. Respons API menggunakan class di `app/Http/Resources/`.

## Model dan Relasi

- `User` merepresentasikan akun. Relasi `complaints()` adalah `hasMany`. Kata sandi otomatis di-hash melalui cast `hashed`; password dan remember token disembunyikan dari serialisasi model. Metode `isAdmin()` memeriksa peran admin.
- `Complaint` merepresentasikan laporan. Relasi `user()` dan `category()` adalah `belongsTo`.
- `Category` merepresentasikan kategori aktif/nonaktif. Relasi `complaints()` adalah `hasMany`.

Gambaran relasi:

```text
User 1 ─── * Complaint * ─── 1 Category
```

Detail kolom, constraint, dan hak akses pengaduan juga dijelaskan di [ERD.md](./ERD.md).

## Database

Migration dalam `database/migrations/` membangun tabel bawaan Laravel serta tabel aplikasi:

- `users`: data akun, termasuk NIM, email, password, peran, dan foto profil.
- `categories`: kategori pengaduan dengan nama/slug unik dan flag `is_active`.
- `complaints`: laporan, relasi user/kategori, deskripsi, lokasi, dan status.
- Tabel pendukung Laravel: `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, dan `failed_jobs`.

Foreign key pengaduan menggunakan `nullOnDelete()` untuk user agar pengaduan tetap tersimpan saat akun dihapus, serta `restrictOnDelete()` untuk kategori yang masih dipakai.

Seeder utama `DatabaseSeeder` membuat kategori contoh, akun demo, dan beberapa pengaduan contoh. Kredensial demo hanya untuk pengembangan lokal; jangan gunakan di lingkungan produksi.

Perintah migrasi dan seeding:

```powershell
php artisan migrate
php artisan db:seed
```

Perintah di atas mengubah database yang dikonfigurasi untuk aplikasi. Pastikan koneksi dan target database sudah benar sebelum menjalankannya.

## Autentikasi dan Hak Akses

- Route yang memerlukan login berada di grup middleware `auth`. Route login/registrasi berada di grup `guest`.
- Login menerima identitas NIM atau email. Percobaan gagal dibatasi per identitas dan alamat IP.
- Setelah login, sesi diregenerasi; saat logout, sesi dibatalkan dan token CSRF diregenerasi.
- `ComplaintPolicy` mengatur akses pengaduan: mahasiswa hanya dapat melihat milik sendiri dan hanya dapat mengubah/menghapus pengaduan sendiri yang masih berstatus `Menunggu`; admin dapat melihat dan menghapus laporan serta mengubah statusnya, tetapi tidak dapat mengedit isi laporan.
- Pembuatan pengaduan hanya diizinkan untuk mahasiswa. Perubahan status, ekspor laporan, dan pengelolaan akun dibatasi sebagai operasi admin oleh pemeriksaan pada controller.
- Daftar pengaduan web, dashboard, dan API secara terpisah membatasi query mahasiswa ke data miliknya.
- Foto profil disimpan di disk `public` Laravel, tetapi endpoint pembacaan foto memeriksa autentikasi pengguna; foto tidak dibaca melalui URL publik langsung dalam alur ini. Form profil hanya mengubah foto; identitas akun tidak dapat diubah di sana.

Untuk aturan rinci, lihat `app/Policies/ComplaintPolicy.php` dan pemeriksaan peran di controller.

## Validasi dan Respons

Controller memakai validasi Laravel (`$request->validate()`), termasuk validasi format dan panjang, keunikan NIM/email, kategori aktif, status yang diizinkan, serta tipe/ukuran file foto.

Jika validasi gagal, Laravel mengembalikan error validasi (umumnya redirect dengan error untuk form web, atau JSON bila request mengharapkan JSON). Pemeriksaan otorisasi gagal dengan HTTP `403`; resource yang tidak ditemukan mengembalikan HTTP `404`; route API yang meminta autentikasi tanpa login mengembalikan HTTP `401` saat request mengharapkan JSON.

Untuk diagnosis error server (HTTP `500`), periksa `storage/logs/laravel.log` dan konfigurasi aplikasi/database. Jangan membagikan nilai rahasia dari `.env` atau menampilkan detail exception ke pengguna produksi.

## Menjalankan dan Menguji Backend

Dari folder root proyek, lingkungan lokal dapat disiapkan sesuai kebutuhan:

```powershell
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Pastikan `.env` berisi konfigurasi database yang benar dan `APP_KEY` telah diatur. Jangan menimpa key aplikasi yang sudah dipakai di lingkungan bersama/produksi.

Jalankan semua tes:

```powershell
php artisan test
```

Tes fitur tersedia di `tests/Feature/`, sedangkan tes unit berada di `tests/Unit/`. Tambahkan atau jalankan tes yang paling dekat dengan perubahan backend yang dikerjakan.

## Troubleshooting

| Gejala | Yang perlu diperiksa |
|---|---|
| HTTP `500` | Baca error terbaru di `storage/logs/laravel.log`; periksa konfigurasi dan exception sebenarnya sebelum mengubah migration. |
| `No application encryption key has been specified` | Atur `APP_KEY` untuk environment yang digunakan; pada lokal baru jalankan `php artisan key:generate`. |
| Koneksi database gagal | Periksa `DB_CONNECTION` dan parameter database di `.env`, lalu pastikan layanan database berjalan. |
| Tabel belum tersedia | Periksa `php artisan migrate:status`; jalankan migration terhadap database yang benar. |
| API mengembalikan `401` | Login terlebih dahulu dan pastikan request membawa sesi autentikasi yang sama. |
| API mengembalikan `403` | Periksa peran user, kepemilikan pengaduan, dan aturan `ComplaintPolicy`/controller. |
