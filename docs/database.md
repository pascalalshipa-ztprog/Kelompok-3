# Panduan Database

Panduan ini merangkum konfigurasi database, migration, relasi, data awal, serta
cara memeriksa skema. Diagram ERD dan kamus data terperinci ada di
[ERD.md](./ERD.md).

## Teknologi dan Konfigurasi

Database lokal default adalah SQLite. MySQL dapat digunakan dengan mengganti
`DB_CONNECTION` dan parameter terkait di `.env`. Jangan menaruh nilai rahasia
atau konfigurasi database produksi di dokumentasi maupun repository.

PHPUnit dikonfigurasi memakai SQLite in-memory (`:memory:`) dan trait
`RefreshDatabase` pada tes fitur. Karena itu tes tidak memakai isi database lokal
atau database MySQL produksi. Migration, relasi, dan operasi data yang
dicakup tes dijalankan di database pengujian yang terisolasi.

## Migration dan Tabel

Delapan berkas migration di `database/migrations/` membangun tabel aplikasi dan
tabel pendukung Laravel:

| Migration | Perubahan utama |
|---|---|
| `0001_01_01_000000_create_users_table.php` | `users`, `password_reset_tokens`, dan `sessions`. |
| `0001_01_01_000001_create_cache_table.php` | `cache` dan `cache_locks`. |
| `0001_01_01_000002_create_jobs_table.php` | `jobs`, `job_batches`, dan `failed_jobs`. |
| `2026_10_29_000001_add_nim_to_users_table.php` | NIM unik dan nullable pada `users`. |
| `2026_10_29_085026_create_categories_table.php` | Tabel kategori. |
| `2026_10_29_085042_create_complaints_table.php` | Tabel pengaduan dan foreign key. |
| `2026_10_29_085117_add_role_to_users_table.php` | Peran `student` atau `admin`. |
| `2026_10_29_115432_add_profile_photo_path_to_users_table.php` | Path foto profil nullable. |

Migration adalah sumber kebenaran skema. Jangan mengedit database lokal secara
manual sebagai pengganti migration.

### Tabel domain

- **`users`**: nama, NIM nullable dan unik, email unik, path foto profil,
  timestamp verifikasi email, peran, password hash, remember token, dan
  timestamps.
- **`categories`**: nama (maksimal 80 karakter) dan slug unik, `is_active`
  boolean bernilai awal `true`, dan timestamps.
- **`complaints`**: `user_id` nullable, `category_id`, judul (maksimal 140
  karakter), deskripsi, lokasi nullable (maksimal 180 karakter), status, dan
  timestamps. Ada indeks pada `(user_id, created_at)` dan `(status, created_at)`.

Status pengaduan yang valid: `Menunggu`, `Diproses`, `Selesai`, dan `Ditolak`.
Detail field dan constraint ada di [ERD.md](./ERD.md).

### Relasi dan aturan penghapusan

- `User hasMany Complaint`; pengaduan merujuk pelapor melalui `user_id`.
- `Category hasMany Complaint`; setiap pengaduan merujuk kategori melalui
  `category_id`.
- Menghapus akun mengubah `complaints.user_id` menjadi `NULL` sehingga laporan
  tetap tersimpan.
- Kategori yang masih digunakan tidak dapat dihapus (`RESTRICT`).

### Data awal

`DatabaseSeeder` membuat kategori Akademik, Fasilitas, Layanan, dan
Kemahasiswaan, serta akun dan pengaduan demo. Seeder memakai operasi
`firstOrCreate`/`updateOrCreate`, sehingga dapat dijalankan ulang untuk data demo.
Kredensial demo hanya untuk pengembangan lokal; ganti sebelum aplikasi dibagikan
atau digunakan pada lingkungan produksi.

## Menyiapkan dan Memeriksa Database

Untuk konfigurasi lokal SQLite bawaan:

```powershell
composer install
php artisan key:generate
php artisan migrate --seed
```

Untuk menjalankan migration dan memeriksa status:

```powershell
php artisan migrate
php artisan migrate:status
```

Untuk MySQL, buat database kosong dan atur `DB_CONNECTION=mysql`, host, port,
nama database, username, dan password di `.env` sebelum menjalankan migration.
Pastikan koneksi yang aktif benar sebelum menjalankan perintah yang mengubah
skema.

Verifikasi pada sesi dokumentasi ini: driver database aktif terkonfigurasi sebagai
SQLite dan seluruh 8 dari 8 migration tercatat `Ran`. Verifikasi ini hanya
mencakup database lokal yang aktif; koneksi MySQL bukan bagian dari pemeriksaan.
Hasil tes migration dan perilaku data tercatat di
[hasil-pengujian.md](./hasil-pengujian.md).

## Backup dan Operasional

- Cadangkan file SQLite menggunakan prosedur yang konsisten dengan aktivitas
  aplikasi; untuk MySQL gunakan mekanisme backup yang dikelola operator.
- Jangan menghapus, menyalin, atau mengubah database produksi saat menjalankan
  tes. PHPUnit memakai database in-memory terpisah.
- Jalankan `php artisan migrate:status` untuk memeriksa migration yang tertunda.
- Jalankan `php artisan migrate --seed` hanya pada database lokal baru atau
  lingkungan yang memang disiapkan untuk seeding. Seeder berisi akun demo.
