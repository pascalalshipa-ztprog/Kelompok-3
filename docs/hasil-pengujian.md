# Laporan Pengujian

## Ringkasan Eksekusi

Suite dijalankan pada 11 November 2026 dengan perintah:

```powershell
php artisan test --compact
```

| Ukuran | Hasil |
|---|---:|
| Tes dijalankan | 86 |
| Lulus | 85 |
| Gagal | 1 |
| Assertions | 440 |
| Durasi | 321,552 ms |

**Status keseluruhan: gagal** karena tes ekspor PDF menerima HTTP 500. Tes lain
yang dijalankan lulus.

## Hasil Per File

| File tes | Jumlah | Hasil/cakupan |
|---|---:|---|
| `tests/Feature/AuthFlowTest.php` | 48 | Halaman autentikasi, registrasi, login, reset dan perubahan kata sandi, sesi, dashboard, pengaduan, hak akses, pencarian, pagination, dan API. |
| `tests/Feature/TestApiTest.php` | 6 | Akses tamu, cakupan data mahasiswa/admin, field resource, pencarian, urutan data, dan pagination API. |
| `tests/Feature/ComplaintReportFeatureTest.php` | 5 | Ekspor CSV/PDF, pembatasan peran, dan struktur template laporan PDF. Tes unduhan PDF gagal karena dependensi runtime tidak tersedia. |
| `tests/Feature/ProfileFeatureTest.php` | 12 | Profil, unggah dan validasi foto, batas ukuran, serta perlindungan data akun. |
| `tests/Feature/UsersAdminFeatureTest.php` | 13 | Pencarian, urutan daftar, dashboard/navigasi peran, dan pengelolaan akun admin. |
| `tests/Feature/ExampleTest.php` | 1 | Respons halaman aplikasi. |
| `tests/Unit/ExampleTest.php` | 1 | Tes unit dasar. |
| **Total** | **86** | **85 lulus, 1 gagal.** |

Nama seluruh 86 metode dan statusnya tercantum di
[inventaris pengujian pada README utama](../README.md#inventaris-kasus-pengujian).

## UI

Tes fitur memeriksa respons HTTP dan konten Blade, termasuk halaman login dan
registrasi, menu dan dashboard sesuai peran, profil, halaman pengaduan, form,
serta feedback validasi. Ini adalah tes integrasi sisi server, bukan tes visual
browser.

Checklist browser desktop dan mobile dalam [panduan frontend](./frontend.md)
belum dijalankan pada verifikasi ini. Proyek tidak memiliki suite browser
otomatis; gambar dashboard di repository adalah aset referensi, bukan bukti
hasil tes terbaru.

## Backend dan API

- `php artisan route:list --except-vendor` berhasil menampilkan route aplikasi.
- Endpoint yang diuji adalah `GET /api/complaints`, memerlukan autentikasi,
  membatasi mahasiswa ke laporan miliknya, dan memberi admin akses ke seluruh
  laporan.
- Enam tes khusus API lulus; coverage mencakup respons 401 untuk tamu, struktur
  resource, pencarian kategori/nama pelapor, urutan ID, dan 10 item per halaman.
- Tes API terarah telah dijalankan ulang pada sesi dokumentasi ini dengan
  `php artisan test tests/Feature/TestApiTest.php --compact`: **6 lulus, 41
  assertions, 4,978 ms**.
- Tes fitur juga mencakup autentikasi, otorisasi, validasi, CRUD, laporan CSV,
  serta template PDF. Endpoint unduhan PDF adalah satu-satunya tes yang gagal
  dalam eksekusi ini.

### Kegagalan yang ditemukan

`tests/Feature/ComplaintReportFeatureTest.php:90`,
`test_admin_can_download_a_pdf_report` mengharapkan HTTP 200 tetapi menerima
HTTP 500. Exception yang tercatat:

```text
Class "Barryvdh\DomPDF\Facade\Pdf" not found
```

Class tersebut berasal dari dependensi
`barryvdh/laravel-dompdf`, yang tercantum di `composer.json` namun tidak tersedia
di `vendor/` pada lingkungan tes ini. Dependensi tidak dipulihkan dalam perubahan
dokumentasi ini. Jalankan `composer install`, lalu ulangi tes tersebut dan suite
penuh.

### Pemulihan kata sandi

Suite menguji tampilan pemulihan, pengiriman notifikasi dengan `Notification::fake`,
token reset valid/tidak valid, dan perubahan kata sandi di database pengujian.
Tes tersebut **tidak** mengirim email melalui SMTP/mail service nyata. Karena
konfigurasi serta verifikasi email pada environment belum selesai, fitur
lupa/reset kata sandi belum dinyatakan siap digunakan pengguna. Rencana
penyelesaian tercantum di [Pengembangan Selanjutnya pada README](../README.md#pengembangan-selanjutnya).

## Database

- Driver database aktif pada saat pemeriksaan: SQLite.
- `php artisan migrate:status`: **8 dari 8 migration berstatus `Ran`** pada
  database SQLite lokal yang aktif.
- Tes fitur menggunakan SQLite in-memory (`DB_DATABASE=:memory:` pada
  `phpunit.xml`) dan `RefreshDatabase`; data tes tidak berasal dari database
  lokal.
- Tes fitur memeriksa penyimpanan dan pembatasan data, termasuk penghapusan akun
  tanpa menghapus laporan terkait.
- Tidak ada tes koneksi atau migration terhadap server MySQL produksi pada
  eksekusi ini.

## Cara Menjalankan Ulang

Pulihkan dependensi proyek bila belum tersedia, kemudian jalankan:

```powershell
composer install
php artisan test
php artisan migrate:status
```

Pastikan koneksi database yang dituju sudah benar sebelum menjalankan migration.
Lihat [panduan database](./database.md) untuk konfigurasi dan operasi migration.

Kasus gagal PDF dapat diisolasi dengan:

```powershell
php artisan test --filter=test_admin_can_download_a_pdf_report
```

Tes unit, fitur, dan regresi yang tercakup suite penuh dijalankan melalui
`php artisan test`; hasil berikutnya harus dicatat bersama tanggal, perintah,
total lulus/gagal, assertion, dan error aktual. Hasil dalam laporan ini merujuk
pada eksekusi yang tercatat di atas, bukan jaminan status setelah perubahan kode
atau lingkungan.
