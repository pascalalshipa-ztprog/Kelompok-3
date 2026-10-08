# Sistem Pengaduan Mahasiswa

## 1. Ringkasan

Sistem Pengaduan Mahasiswa adalah aplikasi web berbasis PHP untuk membantu mahasiswa mengajukan pengaduan atau aspirasi kampus, kemudian memantau proses penanganan melalui dashboard khusus mahasiswa dan admin.

- **Mata kuliah:** Pemrograman Web Lanjut (INF3021), 3 SKS
- **Semester:** 3
- **Model pembelajaran:** Project-Based Learning (PjBL)
- **Nama proyek:** Sistem Pengaduan Mahasiswa
- **Kelompok:** 3
- **Tujuan MVP:** Membuat alur pengajuan, tracking status, dan penanganan pengaduan secara transparan.

## 2. Identitas Kelompok dan Pembagian Peran
|-----------------------------------------------------------------------------------|
| Anggota                       | NIM       | Peran                                 |
------------------------------------------------------------------------------------|
| Cantika Dwi Utari             | 25170023  | Koordinator Proyek                    |
| Hafizh Zaidan Muttaqien       | 25170006  | Backend Developer                     |
| Naufal Marzuki Fadhil         | 25170018  | Frontend/UI Developer                 |
| Paskalino Adrianus Warorowai  | 25170024  | Database & QA Documentation           |
-------------------------------------------------------------------------------------
## 3. Role dan Hak Akses

### 3.1 Mahasiswa

Mahasiswa dapat:

- Mengajukan pengaduan atau aspirasi kampus.
- Mengisi judul, kategori, isi pengaduan, dan lampiran foto.
- Melihat daftar riwayat pengaduan milik sendiri.
- Memantau status pengaduan.
- Melihat tanggapan resmi dari admin.
- Memberikan feedback atau tanggapan kepada pengaduan.

Mahasiswa tidak boleh:

- Mengakses data pengaduan mahasiswa lain.
- Mengubah status pengaduan.
- Mengelola akun pengguna lain.
- Menghapus log audit.

### 3.2 Admin/Petugas

Admin atau petugas dapat:

- Melihat seluruh daftar pengaduan mahasiswa.
- Memverifikasi dan menindaklanjuti pengaduan.
- Mengubah status pengaduan menjadi `Pending`, `Diproses`, `Selesai`, atau `Ditolak`.
- Memberikan tanggapan resmi.
- Melihat laporan rekapitulasi pengaduan.
- Mengelola akun pengguna lain.

Admin atau petugas tidak boleh:

- Mengubah kode sumber sistem tanpa izin.
- Menghapus log audit permanently.

## 4. Requirement Fungsional

### 4.1 Functional Requirements

1. **FR-01 — Login**
   - **Role:** Mahasiswa/Admin
   - **Requirement:** Login menggunakan NIM/email dan password.
   - **Prioritas:** MUST
   - **Acceptance Criteria:** Sistem memvalidasi kredensial dan mengarahkan pengguna ke dashboard sesuai role.

2. **FR-02 — Form Pengaduan**
   - **Role:** Mahasiswa
   - **Requirement:** Mengisi judul, kategori, isi pengaduan, dan lampiran foto.
   - **Prioritas:** MUST
   - **Acceptance Criteria:** Pengaduan disimpan dengan status awal `Pending`, dan lampiran berhasil diunggah.

3. **FR-03 — Riwayat Pengaduan**
   - **Role:** Mahasiswa
   - **Requirement:** Melihat daftar dan detail riwayat pengaduan sendiri.
   - **Prioritas:** MUST
   - **Acceptance Criteria:** Daftar menampilkan pengaduan milik mahasiswa beserta status `Pending`, `Diproses`, `Selesai`, atau `Ditolak`.

4. **FR-04 — Daftar Pengaduan Admin**
   - **Role:** Admin
   - **Requirement:** Melihat seluruh daftar pengaduan mahasiswa.
   - **Prioritas:** MUST
   - **Acceptance Criteria:** Admin dapat melihat semua pengaduan secara terurut beserta detail informasinya.

5. **FR-05 — Status dan Tanggapan Admin**
   - **Role:** Admin
   - **Requirement:** Memperbarui status dan memberikan tanggapan resmi.
   - **Prioritas:** MUST
   - **Acceptance Criteria:** Status diperbarui di database dan tanggapan tampil pada detail pengaduan mahasiswa.

6. **FR-06 — Pencarian dan Filter**
   - **Role:** Mahasiswa/Admin
   - **Requirement:** Mencari dan memfilter pengaduan berdasarkan kategori atau status.
   - **Prioritas:** SHOULD
   - **Acceptance Criteria:** Sistem menampilkan hasil yang relevan berdasarkan kata kunci, kategori, atau status.

7. **FR-07 — Laporan Rekapitulasi**
   - **Role:** Admin
   - **Requirement:** Mengunduh laporan rekapitulasi.
   - **Prioritas:** SHOULD
   - **Acceptance Criteria:** Laporan dapat dibuat dan diunduh berdasarkan tanggal yang dipilih.

8. **FR-08 — Profil dan Password**
   - **Role:** Mahasiswa/Admin
   - **Requirement:** Mengubah profil dan password akun.
   - **Prioritas:** COULD
   - **Acceptance Criteria:** Profil dan password berhasil diperbarui setelah validasi password lama.

### 4.2 Prioritas MoSCoW

- **MUST:** Authentication, form pengaduan, tracking status, verifikasi, dan tanggapan admin.
- **SHOULD:** Search/filter kategori/status dan export laporan rekapitulasi PDF.
- **COULD:** Edit profil dan password.
- **OUT:** Notifikasi real-time menggunakan WebSocket, aplikasi native mobile, dan AI auto-categorization.

## 5. Non-Functional Requirements

| ID | Kategori | Requirement | Cara verifikasi |
|---|---|---|---|
| NFR-01 | Security | Password disimpan dengan hashing Bcrypt, halaman dilindungi middleware berdasarkan role session. | Pemeriksaan hash password, session, dan penetrasi manual. |
| NFR-02 | Usability | Pesan validasi eksplisit dan antarmuka responsif | Menguji input invalid dan tampilan desktop/mobile. |
| NFR-03 | Performance | Halaman utama dapat dimuat dalam rata-rata kurang dari 2 detik; pagination saat lebih dari 10 baris | Mengukur waktu load dan menguji pagination. |
| NFR-04 | Compatibility dan Maintainability | Aplikasi dapat digunakan di browser modern dan source code dikelola melalui Git | Menguji browser Chrome, Edge, Firefox, serta struktur Git repository. |

## 6. Alur Utama

### 6.1 Proses Pengajuan dan Penanganan Pengaduan

1. Mahasiswa membuka halaman utama dan melakukan login.
2. Sistem memvalidasi NIM/email dan password.
3. Jika valid, mahasiswa memasuk dashboard mahasiswa; jika gagal, tampil pesan error.
4. Mahasiswa memilih **Buat Pengaduan**, lalu mengisi judul, kategori, isi, dan lampiran.
5. Sistem memvalidasi input dan lampiran.
6. Jika valid, pengaduan disimpan dengan status `Pending`.
7. Admin login ke portal admin dan membuka dashboard admin.
8. Admin memilih pengaduan dan memilih **Tindak Lanjuti**.
9. Sistem menampilkan detail pengaduan dan form tanggapan.
10. Admin mengubah status menjadi `Diproses` dan memasukkan tanggapan awal.
11. Admin menyelesaikan pengaduan dan mengunggah hasil tindak lanjut.
12. Sistem memperbarui status menjadi `Selesai` dan menyimpan tanggapan.
13. Sistem memperbarui data secara real-time untuk akun mahasiswa.
14. Mahasiswa membuka riwayat pengaduan dan melihat detail serta tanggapan resmi admin.
15. Mahasiswa melihat status akhir dan memberikan konfirmasi atau feedback.

## 7. Inventori Halaman

1. **Login/Masuk — Semua pengguna**
   - **Tujuan:** Mengautentikasi pengguna.
   - **Komponen utama:** NIM/email, password, tombol login, dan alert error.
   - **Prioritas:** MUST

2. **Dashboard Mahasiswa — Mahasiswa**
   - **Tujuan:** Ringkasan pengaduan dan navigasi cepat.
   - **Komponen utama:** Statistik status, tombol pengaduan, dan tabel terbaru.
   - **Prioritas:** MUST

3. **Form Pengaduan — Mahasiswa**
   - **Tujuan:** Menginput pengaduan baru.
   - **Komponen utama:** Judul, kategori, isi, lampiran, dan tombol submit.
   - **Prioritas:** MUST

4. **Riwayat dan Detail Pengaduan — Mahasiswa**
   - **Tujuan:** Melihat daftar, status, dan tanggapan pengaduan.
   - **Komponen utama:** Tabel riwayat, badge status, timeline, dan detail lampiran.
   - **Prioritas:** MUST

5. **Dashboard Admin — Admin**
   - **Tujuan:** Memantau seluruh laporan pengaduan.
   - **Komponen utama:** Total, pending, diproses, selesai, dan chart.
   - **Prioritas:** MUST

6. **Kelola Pengaduan Admin — Admin**
   - **Tujuan:** Memproses dan memverifikasi pengaduan.
   - **Komponen utama:** Tabel, filter, modal, dan form tanggapan.
   - **Prioritas:** MUST

7. **Laporan Rekapitulasi — Admin**
   - **Tujuan:** Mencetak dan mengunduh laporan.
   - **Komponen utama:** Filter tanggal, export PDF/Excel, dan tabel laporan.
   - **Prioritas:** SHOULD

8. **Profil Pengguna — Mahasiswa/Admin**
   - **Tujuan:** Mengelola data dan password.
   - **Komponen utama:** Edit profil, ganti password, dan tombol simpan.
   - **Prioritas:** COULD

## 8. UI/UX Requirements

- Semua halaman MUST memiliki wireframe.
- Wireframe dibuat untuk login, dashboard, form pengaduan, riwayat, dan detail admin.
- Alur perpindahan halaman konsisten dari form pengaduan ke detail riwayat.
- Role dan akses jelas dipisahkan antara halaman mahasiswa dan admin.
- Tombol utama seperti **Kirim Pengaduan** dan **Tindak Lanjuti** terlihat jelas.
- Form pengaduan mencakup judul, kategori, deskripsi, dan upload file.
- Tabel admin dilengkapi tombol detail, ubah status, dan tanggapan.
- Pesan sukses dan error tersedia pada form dan halaman login.
- Layout disusun menggunakan grid Bootstrap atau Tailwind.
- Warna, button, status badge, dan notifkasi harus jelas dan responsif.
- Semua elemen visual harus mendukung MVP dan tidak menambah scope di luar kebutuhan.

## 9. Data dan Implementasi PHP

### 9.1 Sistem data yang disarankan

- **Database:** MySQL
- **Backend:** PHP
- **Arsitektur:** MVC
- **Kerangka:** PHP framework seperti Laravel atau CodeIgniter 4
- **Keamanan password:** Bcrypt
- **Role:** Mahasiswa dan Admin
- **Status pengaduan:** `Pending`, `Diproses`, `Selesai`, `Ditolak`

### 9.2 Data utama

- **users**
  - **Fungsi:** Autentikasi dan hak akses.
  - **Field penting:** `id`, `nim/email`, `password`, `role`, dan `profile`.

- **submissions**
  - **Fungsi:** Mengelola pengaduan mahasiswa.
  - **Field penting:** `id`, `user_id`, `title`, `category`, `description`, `attachment`, `status`, dan `created_at`.

- **submission_comments**
  - **Fungsi:** Menyimpan tanggapan admin.
  - **Field penting:** `id`, `submission_id`, `admin_id`, `message`, dan `created_at`.

- **status_logs**
  - **Fungsi:** Mencatat audit perubahan status.
  - **Field penting:** `id`, `submission_id`, `from_status`, `to_status`, `performed_by`, dan `created_at`.

> Penamaan field database dan komponen form harus konsisten agar migration, model, dan PHP controller mudah dihubungkan.

### 9.3 Struktur repository yang disarankan

```text
README.md
 docs/
   project-charter.md
   requirements.md
   user-flow.png
   wireframe-link.md
 src/
   controllers/
   models/
   views/
   middlewares/
   repositories/
   routes/
   migrations/
   public/
```

## 10. Checklist Submission

- [ ] Semua halaman MVP memiliki wireframe.
- [ ] User flow dari pengajuan ke detail riwayat konsisten.
- [ ] Role dan hak akses sesuai requirement.
- [ ] Tombol utama terlihat jelas.
- [ ] Form pengaduan memiliki judul, kategori, deskripsi, dan upload file.
- [ ] Tabel admin memiliki aksi detail, ubah status, dan tanggapan.
- [ ] Pesan sukses dan error tersedia.
- [ ] Layout responsif menggunakan Bootstrap/Tailwind.
- [ ] Fitur di luar scope tidak ditambahkan tanpa alasan.
- [ ] Nama field database dan input form konsisten.
- [ ] Database, controller, model, dan view mengikuti struktur MVC.

## 11. Refleksi Individual

### Paskalino Adrianus Warorowai

1. **Requirement paling penting:** FR-02 form pengaduan dan FR-05 update status serta tanggapan admin.
2. **Bagian paling berpotensi bingung:** Perubahan status dari `Pending` ke `Diproses` dan `Selesai`.
3. **Scope yang dibatasi:** Notifikasi WebSocket, aplikasi native mobile, dan AI auto-categorization.
4. **Kontribusi minggu ini:** Menyusun spesifikasi data, schema awal, acceptance criteria, dan dokumentasi laporan Minggu 2.
5. **Hal yang harus diperbaiki:** Mendekati konsistensi penamaan field database, input form, dan wireframe Figma.

## 12. Keterangan Implementasi

Aplikasi ini menggunakan bahasa PHP karena seluruh logika backend, autentikasi, pengelolaan data, form pengaduan, dan laporan dapat dibuat dengan PHP. Struktur MVC dapat digunakan agar controller, model, view, dan middleware tetap terpisah. Frontend dapat menggunakan Bootstrap atau Tailwind untuk memastikan tampilan responsif.

## 13. Scope yang Tidak Dikerjakan

- Notifikasi real-time menggunakan WebSocket atau push.
- Aplikasi native untuk mobile.
- AI auto-categorization.
- Integrasi eksternal lain yang tidak tercantum dalam requirement MVP.
