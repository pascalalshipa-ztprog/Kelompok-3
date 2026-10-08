# Sistem Pengaduan Mahasiswa

## 1. Deskripsi Proyek

Sistem Pengaduan Mahasiswa adalah aplikasi berbasis web yang dibuat dengan PHP untuk membantu mahasiswa menyampaikan keluhan, aspirasi, dan kebutuhan terkait kampus. Sistem ini juga memungkinkan admin untuk memproses, menindaklanjuti, dan menanggapi pengaduan secara transparan.

Tujuan utama dari proyek ini adalah:

- Memudahkan mahasiswa mengajukan pengaduan.
- Membantu admin mengelola pengaduan dengan sistem yang terstruktur.
- Menampilkan status pengaduan secara jelas dan mudah dipantau.
- Menciptakan proses komunikasi yang lebih transparan antara mahasiswa dan pihak kampus.

## 2. Informasi Umum

- Mata Kuliah: Pemrograman Web Lanjut (INF3021)
- SKS: 3
- Semester: 3
- Model Pembelajaran: Project-Based Learning (PjBL)
- Nama Proyek: Sistem Pengaduan Mahasiswa
- Kelompok: 3
- Tujuan MVP: Membuat alur pengajuan, tracking status, dan penanganan pengaduan secara jelas.

## 3. Identitas Kelompok

- Cantika Dwi Utari — NIM 25170023 — Koordinator Proyek
- Hafizh Zaidan Muttaqien — NIM 25170006 — Backend Developer
- Naufal Marzuki Fadhil — NIM 25170018 — Frontend/UI Developer
- Paskalino Adrianus Warorowai — NIM 25170024 — Database & QA Documentation

## 4. Role dan Hak Akses

### 4.1 Mahasiswa

Mahasiswa dapat:

- Mengajukan pengaduan atau aspirasi.
- Mengisi judul, kategori, isi pengaduan, dan lampiran.
- Melihat riwayat pengaduan milik sendiri.
- Memantau status pengaduan.
- Membaca tanggapan dari admin.
- Memberikan feedback atau respon terkait pengaduan.

Mahasiswa tidak boleh:

- Mengakses pengaduan milik mahasiswa lain.
- Mengubah status pengaduan.
- Mengelola akun pengguna lain.
- Menghapus log audit sistem.

### 4.2 Admin / Petugas

Admin dapat:

- Melihat seluruh daftar pengaduan.
- Memverifikasi pengaduan yang masuk.
- Mengubah status pengaduan menjadi Pending, Diproses, Selesai, atau Ditolak.
- Memberikan tanggapan resmi kepada mahasiswa.
- Melihat laporan rekapitulasi pengaduan.
- Mengelola akun pengguna lain.

Admin tidak boleh:

- Mengubah kode sumber sistem tanpa izin.
- Menghapus log audit secara permanen.

## 5. Requirement Fungsional

### 5.1 Fitur Utama

- Login untuk mahasiswa dan admin.
- Form pengaduan dengan judul, kategori, isi, dan lampiran.
- Riwayat pengaduan untuk setiap mahasiswa.
- Tabel daftar pengaduan untuk admin.
- Update status dan respons admin.
- Pencarian dan filter berdasarkan kategori atau status.
- Laporan rekapitulasi pengaduan.
- Pengaturan profil dan password pengguna.

### 5.2 Prioritas

- Must Have: autentikasi, form pengaduan, tracking status, verifikasi, dan respons admin.
- Should Have: pencarian, filter, dan laporan rekapitulasi.
- Could Have: edit profil dan ganti password.
- Out of Scope: notifikasi real-time, aplikasi mobile native, dan AI auto-categorization.

## 6. Requirement Non-Fungsional

- Security: password disimpan dengan hashing Bcrypt dan akses diatur berdasarkan role session.
- Usability: pesan validasi jelas dan antarmuka responsif.
- Performance: halaman utama dapat dimuat dengan cepat dan tetap nyaman saat data banyak.
- Compatibility: aplikasi dapat diakses di browser modern dan dikelola melalui Git dengan struktur proyek yang rapi.

## 7. Alur Proses Pengaduan

1. Mahasiswa login ke sistem.
2. Mahasiswa mengajukan pengaduan baru.
3. Sistem menyimpan pengaduan dengan status awal Pending.
4. Admin membuka daftar pengaduan dan memeriksa isinya.
5. Admin menindaklanjuti pengaduan dan mengubah status.
6. Admin memberikan tanggapan resmi.
7. Mahasiswa melihat hasil penanganan dan status akhir.
8. Mahasiswa dapat memberikan feedback terhadap solusi yang diberikan.

## 8. Struktur Halaman Utama

- Halaman Login
- Dashboard Mahasiswa
- Form Pengaduan
- Riwayat Pengaduan
- Dashboard Admin
- Kelola Pengaduan Admin
- Laporan Rekapitulasi
- Profil Pengguna

## 9. Teknologi yang Disarankan

- Bahasa pemrograman: PHP
- Database: MySQL
- Arsitektur: MVC
- Framework: Laravel atau CodeIgniter 4
- Security: Bcrypt
- Frontend: Bootstrap atau Tailwind

## 10. Struktur Database yang Direkomendasikan

- users
  - Menyimpan data akun dan role pengguna.
- submissions
  - Menyimpan data pengaduan mahasiswa.
- submission_comments
  - Menyimpan tanggapan dari admin.
- status_logs
  - Menyimpan riwayat perubahan status pengaduan.

## 11. Struktur Repository

- README.md
- docs/
- src/
- controllers/
- models/
- views/
- middlewares/
- repositories/
- routes/
- migrations/
- public/

## 12. Checklist Penyelesaian

- Semua halaman MVP memiliki wireframe.
- Alur pengajuan sampai detail riwayat konsisten.
- Role dan hak akses sesuai kebutuhan.
- Tombol utama terlihat jelas.
- Form pengaduan memiliki judul, kategori, isi, dan upload file.
- Tabel admin memiliki aksi detail, ubah status, dan tanggapan.
- Pesan sukses dan error tersedia.
- Layout responsif menggunakan Bootstrap atau Tailwind.
- Fitur di luar scope tidak ditambahkan tanpa alasan.
- Nama field database dan input form konsisten.
- Struktur MVC diterapkan dengan rapi.

## 13. Kesimpulan

Proyek Sistem Pengaduan Mahasiswa ini dirancang untuk membantu kampus dalam menangani pengaduan mahasiswa secara lebih cepat, terstruktur, dan transparan. Dengan pendekatan MVC, penggunaan PHP, dan fitur utama yang terarah, sistem ini sangat sesuai untuk kebutuhan MVP dalam pembelajaran Pemrograman Web Lanjut.
