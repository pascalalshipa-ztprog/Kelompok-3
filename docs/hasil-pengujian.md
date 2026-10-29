# Hasil Pengujian Otomatis

## Ringkasan Terakhir

Perintah `php artisan test` dijalankan pada 30 Oktober 2026.

| Hasil | Jumlah |
|---|---:|
| Tes lulus | 61 |
| Tes gagal | 0 |
| Assertions | 274 |
| Durasi | 8,399 detik |

**Status: lulus.** Seluruh tes pada eksekusi tersebut berhasil.

## Rincian Berdasarkan File Tes

| File | Jumlah tes | Cakupan utama |
|---|---:|---|
| `tests/Feature/AuthFlowTest.php` | 38 | Halaman autentikasi, registrasi, login mahasiswa dan admin, pembatasan percobaan login, pemulihan dan penggantian kata sandi, sesi, pengaduan, hak akses, pencarian, pagination, dan API |
| `tests/Feature/ComplaintReportFeatureTest.php` | 2 | Ekspor CSV oleh admin dan larangan ekspor untuk mahasiswa |
| `tests/Feature/ProfileFeatureTest.php` | 10 | Akses dan pembaruan profil, unggah foto, validasi foto, serta perlindungan nama dan NIM mahasiswa |
| `tests/Feature/UsersAdminFeatureTest.php` | 9 | Daftar dan pencarian pengguna, hak akses, pembaruan, dan penghapusan akun oleh admin |
| `tests/Feature/ExampleTest.php` | 1 | Respons halaman aplikasi |
| `tests/Unit/ExampleTest.php` | 1 | Tes unit dasar |
| **Total** | **61** | |

## Menjalankan Ulang

Dari direktori utama proyek, jalankan:

```powershell
php artisan test
```

Hasil dalam dokumen ini merekam satu eksekusi tertentu. Jalankan kembali perintah
di atas untuk mengetahui status tes pada versi kode dan lingkungan yang sedang
digunakan.
