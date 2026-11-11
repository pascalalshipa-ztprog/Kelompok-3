# Panduan Frontend

Dokumen ini menjelaskan antarmuka web SuaraKampus, halaman untuk mahasiswa dan
admin, aset visual, serta cara memeriksa UI.

## Teknologi dan Struktur

Frontend dirender di server menggunakan Blade dan HTML semantik. CSS ditulis
langsung di `public/css/`; aplikasi tidak memakai Node.js, npm, Vite, atau proses
bundling. Layout portal memuat sedikit JavaScript inline di halaman daftar
pengaduan untuk memfilter baris berdasarkan kategori pada halaman yang sedang
dibuka. Font DM Sans dan Manrope dimuat dari Google Fonts dengan fallback
sans-serif.

| Lokasi | Fungsi |
|---|---|
| `resources/views/layouts/auth.blade.php` | Layout halaman login dan autentikasi. |
| `resources/views/layouts/portal.blade.php` | Layout portal setelah login, termasuk navigasi berdasarkan peran, pesan status, dan error. |
| `resources/views/auth/` | Halaman login, registrasi, lupa kata sandi, dan reset kata sandi. |
| `resources/views/dashboard/` | Dashboard mahasiswa dan admin. |
| `resources/views/complaints/` | Daftar, form, detail, dan kontrol status pengaduan. |
| `resources/views/profile/` | Profil, unggah foto, dan form perubahan kata sandi. |
| `resources/views/users/` | Daftar dan form pengelolaan akun mahasiswa oleh admin. |
| `resources/views/reports/` | Template laporan PDF. |
| `resources/views/errors/` | Halaman error 403, 404, dan 500. |
| `public/css/auth.css` | Gaya khusus halaman autentikasi. |
| `public/css/portal.css` | Gaya portal, dashboard, tabel, form, dan tata letak responsif. |
| `docs/dashboard-mahasiswa.png`, `docs/dashboard-admin.png` | Gambar referensi dashboard yang disimpan di repositori. |

## Halaman dan Alur

### Mahasiswa

1. Masuk dengan NIM atau email, atau mendaftar dengan nama, NIM, email, dan kata
   sandi. Halaman lupa/reset kata sandi tersedia dalam UI, tetapi pengiriman
   email sebenarnya belum diverifikasi; fitur ini belum siap digunakan sampai
   mail service dikonfigurasi dan diuji.
2. Dashboard menampilkan jumlah pengaduan dan lima laporan terbaru milik akun.
3. Form pengaduan menerima kategori aktif, judul, deskripsi, dan lokasi opsional.
4. Riwayat dapat dicari dan dipaginasi; filter kategori hanya menyaring baris
   pada halaman yang sedang ditampilkan.
5. Detail laporan menampilkan nomor tiket, status, kategori, pelapor, lokasi,
   isi, dan tanggal.
6. Pemilik dapat mengubah atau menghapus laporannya sendiri hanya ketika
   statusnya `Menunggu`.
7. Halaman profil menampilkan identitas hanya-baca, menerima foto JPG/PNG/WEBP
   maksimal 500 KB, dan menyediakan form perubahan kata sandi.

### Admin

1. Dashboard merangkum laporan menurut status dan akun menurut peran.
2. Semua laporan dapat dicari, dibuka, dan diberi status baru dari daftar.
3. Admin dapat mengunduh laporan CSV atau PDF.
4. Manajemen pengguna menampilkan akun mahasiswa, dengan pencarian, pagination,
   ubah, dan hapus; akun admin tidak dapat dikelola di halaman tersebut.
5. Profil admin menyediakan unggah foto dan perubahan kata sandi seperti profil
   mahasiswa.

## Perilaku UI dan Validasi

- Form menggunakan label dan kontrol HTML standar; validasi server Laravel
  menampilkan pesan kesalahan dekat field atau di dalam pemberitahuan.
- Status berhasil ditampilkan melalui elemen `role="status"` dan kesalahan
  melalui `role="alert"`.
- Navigasi menandai halaman aktif dengan `aria-current="page"`; tabel dan
  navigasi menggunakan nama aksesibel.
- CSS portal menyesuaikan layout pada breakpoint 1000, 850, dan 700 piksel;
  layout autentikasi menyesuaikan pada 520 piksel. Preferensi `prefers-reduced-motion`
  juga ditangani.
- Gambar profil dilayani melalui route terautentikasi, bukan URL file publik
  langsung.

## Pemeriksaan UI

Tes fitur Laravel membuat request HTTP dan memeriksa respons halaman Blade,
navigasi sesuai peran, form, pesan validasi, serta perilaku aksi. Tes tersebut
bukan pengujian visual browser dan tidak mengukur layout pada perangkat nyata.
Suite browser otomatis tidak dikonfigurasi di proyek ini.

Checklist pemeriksaan manual setelah menjalankan aplikasi:

1. Buka `/login`, periksa tampilan desktop dan lebar 375 px; lanjutkan ke
   registrasi dan pemulihan kata sandi.
2. Masuk sebagai mahasiswa demo, periksa dashboard, buat pengaduan, cari/filter
   daftar, buka detail, lalu coba edit dan hapus laporan berstatus `Menunggu`.
3. Periksa profil: unggah foto berformat yang diizinkan, tinjau pesan untuk file
   yang terlalu besar, dan ubah kata sandi dengan kata sandi saat ini.
4. Masuk sebagai admin demo, periksa dashboard, ubah status pengaduan, daftar
   pengguna, serta unduhan CSV/PDF.
5. Ulangi pada viewport desktop dan ponsel; periksa navigasi, tabel, form,
   pesan validasi, dan tidak adanya overflow horizontal yang tidak diinginkan.

Status checklist manual UI: **belum dijalankan dalam verifikasi dokumentasi ini**.
Gambar dashboard di atas adalah aset referensi repositori dan bukan bukti
pengujian visual terbaru.

Jalankan server lokal dengan `php artisan serve`; buka
`http://127.0.0.1:8000`. Akun demo dan langkah setup database ada di
[README utama](../README.md).
