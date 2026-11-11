## Informasi Proyek

- Nama        : Sistem Pengaduan Mahasiswa
- Kelompok    : 3
- Mata kuliah : Pemrograman Web Lanjut (INF3021)

### Anggota dan Kontribusi

Kolom tugas mengikuti pembagian yang tercantum di README proyek. Ringkasan
kontribusi merujuk pada pesan commit dan berkas yang berubah; beberapa pekerjaan
melintasi frontend dan backend. Commit gabungan tidak selalu menunjukkan
seluruh pekerjaan kolaboratif.

| Anggota | Tugas proyek | Kontribusi yang tercatat |
|---|---|---|
| Cantika Dwi Utari — NIM 25170023 | Dokumentasi | Memperbarui informasi proyek dan anggota di README (`4af2987`), serta berkontribusi pada dokumentasi proyek. |
| Hafizh Zaidan Muttaqien — NIM 25170006 | Backend | Implementasi akses API pengaduan baca-saja per peran, pemisahan route dan dashboard, sinkronisasi backend, serta tes API (`1d64e46`, `23f0ad5`, `20e3e1f`). |
| Naufal Marzuki Fadhil — NIM 25170018 | Frontend | Pembaruan UI responsif pada CSS dan halaman pengaduan, serta pemisahan dashboard mahasiswa/admin (`93d7a93`, `fa1cace`). |
| Paskalino Adrianus Warorowai — NIM 25170024 | Koordinator dan database | Menyiapkan proyek dan komponen inti aplikasi, serta mengembangkan dokumentasi backend (`a02122f`, `4d282ca`). Riwayat commit menggunakan nama Pascal Ztecno. |

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
Alur lupa/reset kata sandi belum siap digunakan secara nyata: pengiriman tautan
email belum dikonfigurasi dan diverifikasi dengan layanan email. Lihat bagian
[Pengembangan Selanjutnya](#pengembangan-selanjutnya).

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

## Pengujian dan Status Verifikasi

Verifikasi suite penuh sebelumnya dijalankan pada 11 November 2026. Perintahnya:

```powershell
php artisan test --compact
```

| Area | Pemeriksaan | Hasil |
|---|---|---|
| UI | Tes fitur HTTP merender halaman Blade dan memeriksa autentikasi, navigasi berbasis peran, dashboard, profil, pengaduan, manajemen pengguna, serta validasi. | Kasus UI server-side lulus; satu kegagalan suite adalah ekspor PDF, bukan render UI. Pengujian visual/responsif di browser belum dilakukan; ikuti checklist di [panduan frontend](docs/frontend.md). |
| Backend/API | Route terdaftar; tes API memeriksa autentikasi, cakupan data per peran, pencarian, urutan, pagination, dan bentuk JSON. | `route:list` berhasil. Tes API terarah yang dijalankan ulang pada sesi dokumentasi ini: **6 lulus, 41 assertions**. |
| Database | Status migration pada database lokal SQLite aktif; tes fitur memakai SQLite in-memory. | 8 dari 8 migration berstatus `Ran`. Server MySQL tidak diuji. |
| Suite | 86 tes, 440 assertions. | 85 lulus, 1 gagal. |

Satu kegagalan adalah tes unduhan PDF
`ComplaintReportFeatureTest::test_admin_can_download_a_pdf_report`: request
berakhir HTTP 500 karena class
`Barryvdh\DomPDF\Facade\Pdf` tidak ditemukan pada lingkungan yang diuji.
Dependensi `barryvdh/laravel-dompdf` tercantum di `composer.json`; pulihkan
dependensi dengan `composer install`, lalu jalankan ulang tes PDF dan suite.
Kegagalan ini belum diperbaiki dalam perubahan dokumentasi ini.

### Cara Menguji API

API yang tersedia saat ini hanya **`GET /api/complaints`** (baca-saja) dan
memerlukan sesi login Laravel. Belum tersedia endpoint API untuk membuat,
mengubah, atau menghapus pengaduan. Jalankan aplikasi lokal dengan `php artisan
serve`, lalu gunakan Postman atau klien HTTP lain:

1. Pastikan database sudah dimigrasikan dan data demo tersedia. Login ke aplikasi
   melalui browser/Postman sebagai mahasiswa atau admin. Untuk Postman, mulai
   dengan `GET http://127.0.0.1:8000/login`, pertahankan cookie sesi, lalu kirim
   form `POST /login` dengan field `identity` (NIM atau email) dan `password`.
   Sertakan token CSRF `_token` dari form login; cookie dan token sesi harus
   berasal dari sesi yang sama.
2. Kirim `GET http://127.0.0.1:8000/api/complaints` dengan header
   `Accept: application/json` dan cookie sesi login yang sama.
3. Uji pencarian dan pagination dengan
   `GET /api/complaints?search=lampu&page=1`.
4. Ulangi dengan akun mahasiswa (hanya laporannya sendiri) dan admin (semua
   laporan). Tanpa login, request yang menerima JSON seharusnya memberi HTTP
   `401`. Respons sukses berisi `data`, `links`, dan `meta`; data per halaman
   maksimal 10 laporan.
5. Untuk pengujian otomatis API yang terisolasi, dari root proyek jalankan:

```powershell
php artisan test tests/Feature/TestApiTest.php --compact
php artisan route:list --path=api/complaints
```

Tes otomatis terakhir untuk file API menghasilkan 6 tes lulus dan 41
assertions. Tes API menggunakan SQLite in-memory; langkah Postman di atas
menguji sesi aplikasi lokal yang sedang berjalan.

### Cara Menjalankan Semua Tes

Jalankan semua suite PHPUnit:

```powershell
php artisan test
```

Perintah berikut berguna untuk memeriksa satu kelompok tes atau mengulang satu
kasus tertentu:

```powershell
php artisan test tests/Feature/AuthFlowTest.php
php artisan test tests/Feature/ComplaintReportFeatureTest.php
php artisan test --filter=test_admin_can_download_a_pdf_report
php artisan migrate:status
```

Daftar setiap kasus, statusnya pada eksekusi suite penuh, dan cakupan dirinci di
bagian [Inventaris kasus pengujian](#inventaris-kasus-pengujian) serta
[laporan pengujian](docs/hasil-pengujian.md). Laporan suite penuh yang tercatat:
**86 tes; 85 lulus; 1 gagal; 440 assertions**. Tes API terarah yang dijalankan
ulang terpisah: **6 lulus; 41 assertions**.

Tes PHPUnit menggunakan database SQLite in-memory sesuai `phpunit.xml`, bukan
data SQLite lokal atau database MySQL produksi. Untuk menjalankan ulang:

```powershell
php artisan test
php artisan migrate:status
```

### Inventaris Kasus Pengujian

Berikut seluruh 86 metode tes (85 nama unik; kasus tamu API ada di dua file) yang
ditemukan di suite dan statusnya dalam eksekusi penuh pada 11 November 2026.
Semua lulus kecuali kasus PDF yang ditandai **GAGAL** (85 lulus, 1 gagal).

<details>
<summary>AuthFlowTest.php — 48 kasus</summary>

- `test_root_redirects_to_the_login_page`
- `test_login_page_is_rendered`
- `test_login_page_links_to_password_recovery`
- `test_password_recovery_page_is_rendered`
- `test_password_recovery_sends_a_reset_notification_to_the_registered_user`
- `test_password_recovery_does_not_disclose_unknown_email_addresses`
- `test_valid_reset_token_changes_password_and_returns_to_login`
- `test_invalid_reset_token_does_not_change_password`
- `test_register_page_is_rendered`
- `test_password_change_form_is_available_on_the_profile_not_the_sidebar`
- `test_student_can_change_password_with_their_current_password`
- `test_student_cannot_change_password_without_the_correct_current_password`
- `test_admin_can_change_password_from_their_profile`
- `test_student_registration_creates_a_hashed_student_account`
- `test_registration_rejects_an_existing_nim`
- `test_registration_rejects_an_existing_email`
- `test_login_accepts_a_student_nim`
- `test_remember_me_restores_the_login_after_the_session_is_lost`
- `test_login_without_remember_does_not_create_a_recaller_cookie`
- `test_login_accepts_an_admin_email`
- `test_student_login_ignores_an_intended_admin_dashboard`
- `test_student_login_ignores_an_intended_admin_complaint_page`
- `test_admin_login_ignores_an_intended_student_dashboard`
- `test_admin_login_ignores_an_intended_student_complaint_page`
- `test_login_rejects_an_invalid_password`
- `test_login_is_locked_for_two_minutes_after_three_failed_attempts`
- `test_portal_uses_server_side_session_expiration_without_javascript`
- `test_guest_is_redirected_from_the_complaint_list`
- `test_role_specific_complaint_routes_are_direct_and_restricted`
- `test_guest_cannot_access_the_complaint_api`
- `test_student_can_create_a_complaint`
- `test_student_cannot_create_a_complaint_with_an_invalid_category`
- `test_student_can_view_only_their_own_complaints`
- `test_complaint_list_is_ordered_by_ascending_id`
- `test_student_dashboard_shows_five_latest_complaints_in_ascending_id_order`
- `test_student_can_edit_own_pending_complaint_without_changing_status`
- `test_student_cannot_edit_another_students_complaint`
- `test_student_cannot_edit_a_complaint_after_processing_started`
- `test_student_can_delete_their_own_pending_complaint`
- `test_admin_can_view_all_complaints_and_update_status_without_editing_them`
- `test_admin_can_update_status_inline_and_return_to_the_same_list_page`
- `test_admin_sees_inline_status_controls_in_the_complaint_list`
- `test_student_list_does_not_render_inline_status_controls`
- `test_student_cannot_update_complaint_status_inline`
- `test_complaint_search_matches_title_and_category`
- `test_complaint_api_returns_only_the_demo_students_own_records`
- `test_complaint_api_returns_all_records_to_the_demo_admin`
- `test_complaint_list_is_paginated`

</details>

<details>
<summary>TestApiTest.php — 6 kasus (seluruhnya lulus pada tes API terarah)</summary>

- `test_guest_cannot_access_the_complaint_api`
- `test_student_receives_only_their_complaints_with_resource_fields`
- `test_admin_receives_all_complaints_and_can_search_by_category`
- `test_admin_can_search_complaints_by_reporter_name`
- `test_complaint_api_orders_records_by_ascending_id`
- `test_complaint_api_returns_ten_items_per_page`

</details>

<details>
<summary>ComplaintReportFeatureTest.php — 5 kasus</summary>

- `test_admin_can_download_a_structured_excel_compatible_csv_report`
- `test_student_cannot_download_the_admin_complaint_report`
- `test_admin_can_download_a_pdf_report` — **GAGAL**: HTTP 500, facade DomPDF tidak ditemukan.
- `test_student_cannot_download_the_admin_pdf_report`
- `test_pdf_report_view_includes_explanation_chart_and_workflow_diagram`

</details>

<details>
<summary>ProfileFeatureTest.php — 12 kasus</summary>

- `test_student_can_view_their_profile_page`
- `test_admin_can_view_their_profile_page`
- `test_guest_is_redirected_from_profile_page`
- `test_student_can_update_profile_and_upload_a_photo_that_persists`
- `test_student_cannot_change_account_details_from_the_profile_update_request`
- `test_admin_can_upload_a_profile_photo_without_changing_account_details`
- `test_admin_cannot_change_account_details_from_the_profile_update_request`
- `test_admin_nim_is_unchanged_when_updating_profile`
- `test_invalid_profile_photo_is_rejected_without_changing_profile`
- `test_profile_photo_larger_than_500_kb_is_rejected`
- `test_submitted_photo_path_cannot_be_used_to_overwrite_profile_photo_path`
- `test_replacing_a_profile_photo_removes_the_previous_file`

</details>

<details>
<summary>UsersAdminFeatureTest.php — 13 kasus</summary>

- `test_admin_can_view_and_search_users`
- `test_admin_accounts_are_excluded_from_user_management_actions`
- `test_admin_user_directory_orders_students_by_nim_ascending`
- `test_admin_sidebar_shows_administration_role_and_user_management`
- `test_admin_dashboard_shows_user_and_complaint_pie_chart_totals`
- `test_student_sidebar_shows_student_menu_without_admin_links`
- `test_student_cannot_access_user_management`
- `test_legacy_users_route_remains_available_to_admins_only`
- `test_admin_can_update_user_details_without_changing_their_role`
- `test_admin_cannot_change_a_student_email`
- `test_admin_can_delete_a_student_and_keep_their_complaints`
- `test_admin_cannot_delete_their_own_account`
- `test_admin_cannot_delete_another_admin`

</details>

<details>
<summary>Example tests — 2 kasus</summary>

`tests/Feature/ExampleTest.php`:

- `test_the_application_returns_a_successful_response`

`tests/Unit/ExampleTest.php`:

- `test_that_true_is_true`

</details>

### Bug yang Sudah Ditangani

Riwayat commit dan tes regresi mencatat perbaikan/perlindungan berikut:

| Masalah/risiko | Perbaikan atau perlindungan yang tercatat | Bukti |
|---|---|---|
| Setelah login, tujuan redirect dari role lain bisa membawa pengguna ke area yang tidak sesuai. | Redirect login diarahkan sesuai role dan tujuan role yang berbeda diabaikan. | Commit `a526c7d`; empat tes redirect mahasiswa/admin di `AuthFlowTest.php`. |
| Halaman akses ditolak (403) tidak menyediakan jalan kembali yang sesuai. | Halaman 403 menyediakan tombol kembali ke dashboard sesuai role (atau login untuk tamu). | Commit `a64cfef`, dipulihkan pada `215a77e`; `resources/views/errors/403.blade.php`. |
| Akses tidak sesuai kepemilikan atau role pada pengaduan dan manajemen akun. | Policy, pembatasan query, dan pemeriksaan role menjaga akses; kasus-kasus ini diuji sebagai tes regresi. | Tes ownership, update status, dan user admin pada `AuthFlowTest.php`/`UsersAdminFeatureTest.php`. |
| Penghapusan akun mahasiswa berisiko menghilangkan laporan terkait. | Foreign key menyimpan laporan dengan `user_id = NULL` saat akun dihapus. | Migration pengaduan dan `test_admin_can_delete_a_student_and_keep_their_complaints`. |
| Nilai laporan yang diawali formula spreadsheet berisiko dieksekusi saat membuka CSV. | Sel CSV diawali apostrof untuk nilai yang tampak seperti formula. | `test_admin_can_download_a_structured_excel_compatible_csv_report`. |

Baris terakhir adalah perlindungan yang diuji pada kode saat ini; bukan klaim
bahwa riwayat commit menyebutnya sebagai insiden tersendiri. Daftar ini
merangkum hal yang dapat diverifikasi dari commit dan tes yang tersedia, bukan
jaminan bahwa seluruh bug lama atau bug yang belum dilaporkan sudah ditemukan.

## Pengembangan Selanjutnya

Prioritas berikut adalah rencana, bukan fitur yang dinyatakan telah selesai:

1. **Selesaikan pemulihan/reset kata sandi untuk penggunaan nyata.** Route,
   tampilan, controller, dan tes otomatis untuk token/notifikasi sudah ada,
   tetapi pengiriman email sebenarnya belum dikonfigurasi atau diuji di
   environment aplikasi. Konfigurasikan SMTP/mail service di `.env`, uji
   penerimaan email dan masa berlaku token dari awal sampai akhir, penanganan
   token kedaluwarsa, serta respons kegagalan mail. Jangan menaruh credential
   mail dalam repository. Sampai diverifikasi, jangan anggap lupa/reset kata
   sandi sudah siap dipakai pengguna.
2. **Pulihkan dan verifikasi ekspor PDF.** Dependensi DomPDF tercatat di manifest
   tetapi class facade tidak ada pada lingkungan tes saat laporan dibuat.
   Jalankan `composer install`, ulangi tes PDF, lalu uji unduhan nyata.
3. **Lakukan tes UI manual lintas perangkat dan browser.** Uji login, alur
   pengaduan, profil, manajemen akun, aksesibilitas keyboard, responsivitas, dan
   tampilan error; simpan hasil/checklist dengan tanggal dan environment.
4. **Rencanakan cakupan API berikutnya.** Bila diperlukan, tetapkan kontrak,
   validasi, otorisasi, respons, serta tes integrasi untuk membuat, memperbarui,
   dan menghapus pengaduan. Endpoint sekarang hanya baca-saja; jangan
   menganggap operasi tulis sudah tersedia.
5. **Verifikasi kompatibilitas MySQL dan operasional data.** Jalankan migration
   serta tes integrasi di database MySQL terisolasi, dan siapkan prosedur backup
   serta pemulihan yang sesuai environment.
6. **Tinjau skalabilitas laporan.** Ekspor PDF saat ini memuat semua laporan ke
   memori; uji volume realistis dan optimalkan/paginasi bila data berkembang.

### Dokumentasi

- [Panduan frontend](docs/frontend.md) — halaman, alur UI, aset, dan checklist pemeriksaan.
- [Panduan backend](docs/README.md) — struktur backend, route, controller, keamanan akses, dan alur request.
- [Panduan database](docs/database.md) — migration, konfigurasi, operasi, dan pemeriksaan database.
- [ERD dan kamus data](docs/ERD.md) — relasi, field, constraint, dan hak akses.
- [Laporan pengujian](docs/hasil-pengujian.md) — rincian hasil suite dan batasan verifikasi.

Catatan kapasitas: ekspor CSV membaca data secara bertahap, tetapi ekspor PDF
saat ini memuat seluruh pengaduan sekaligus ke memori untuk dirender. Jika jumlah
laporan bertambah besar, unduhan PDF dapat menjadi lambat atau membutuhkan
memori lebih besar.

## Struktur Proyek

- `app/` — model, controller, policy, dan logika aplikasi.
- `database/migrations/` — skema database.
- `database/seeders/` — kategori dan data demo.
- `resources/views/` — halaman Blade.
- `public/css/` — stylesheet statis.
- `routes/web.php` — rute halaman dan endpoint JSON.
- `tests/` — tes fitur dan unit.
- `docs/` — dokumentasi frontend, backend, database, ERD, dan laporan pengujian.
