<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Dapatkan data pengaduan untuk panel admin.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getReports(): array
    {
        return [
            [
                'id' => 'PGD-2025-084',
                'title' => 'AC ruang kelas Gedung B tidak berfungsi',
                'category' => 'Fasilitas dan Sarana Prasarana',
                'location' => 'Gedung B Lantai 3, Ruang B.301',
                'date' => '22 Mei 2025',
                'status' => 'Diproses',
                'reporter' => 'Naufal Marzuki (25170018)',
                'description' => 'AC di ruang B.301 mengeluarkan suara bising dan hembusan angin tidak dingin sejak 3 hari yang lalu, mengganggu kenyamanan perkuliahan mahasiswa.',
                'response' => 'Petugas teknisi sarana dan prasarana telah menjadwalkan pengecekan unit kompresor pendingin ruangan pada Jumat pagi.',
                'attachment' => 'foto_ac_rusak.jpg',
            ],
            [
                'id' => 'PGD-2025-071',
                'title' => 'Jaringan Wi-Fi perpustakaan tidak stabil',
                'category' => 'Layanan Digital & Fasilitas',
                'location' => 'Perpustakaan Pusat Lt. 2',
                'date' => '18 Mei 2025',
                'status' => 'Selesai',
                'reporter' => 'Naufal Marzuki (25170018)',
                'description' => 'Koneksi Wi-Fi sering terputus saat jam ramai (12.00 - 15.00 WIB) sehingga menyulitkan mahasiswa yang sedang mengerjakan tugas akhir.',
                'response' => 'Access point di ruang baca barat telah diganti dengan perangkat throughput tinggi serta alokasi bandwidth ditingkatkan.',
                'attachment' => null,
            ],
            [
                'id' => 'PGD-2025-063',
                'title' => 'Keterlambatan input nilai KHS Semester Ganjil',
                'category' => 'Akademik',
                'location' => 'Fakultas Ilmu Komputer',
                'date' => '10 Mei 2025',
                'status' => 'Selesai',
                'reporter' => 'Naufal Marzuki (25170018)',
                'description' => 'Nilai mata kuliah Pemrograman Web Lanjut belum muncul di portal akademik padahal batas pengisian KRS semester baru sudah dekat.',
                'response' => 'Dosen pengampu telah mengunggah nilai akhir ke sistem akademik BAAK. Nilai kini dapat diakses di portal SIAKAD.',
                'attachment' => null,
            ],
            [
                'id' => 'PGD-2025-052',
                'title' => 'Klarifikasi penagihan ganda praktikum laboratorium',
                'category' => 'Administrasi dan Keuangan',
                'location' => 'Bagian Keuangan Rektorat',
                'date' => '28 Apr 2025',
                'status' => 'Selesai',
                'reporter' => 'Naufal Marzuki (25170018)',
                'description' => 'Tercatat tagihan praktikum dua kali pada invoice semester 4. Mohon bantuan untuk penyesuaian nominal.',
                'response' => 'Biro Keuangan telah mengoreksi invoice terkait. Kelebihan pembayaran telah dikonfirmasi dan dialihkan ke periode berikutnya.',
                'attachment' => 'bukti_pembayaran.pdf',
            ],
            [
                'id' => 'PGD-2025-045',
                'title' => 'Proyektor LCD Gedung C sering mati mendadak',
                'category' => 'Fasilitas dan Sarana Prasarana',
                'location' => 'Ruang C.204',
                'date' => '15 Apr 2025',
                'status' => 'Diproses',
                'reporter' => 'Dian Prasetyo (25170034)',
                'description' => 'Lampu proyektor sering mati sendiri di tengah sesi presentasi kuliah dikarenakan indikator overheat menyala.',
                'response' => 'Unit pendingin proyektor sedang dibersihkan dan unit lampu cadangan telah dipasang sementara.',
                'attachment' => null,
            ],
        ];
    }

    /**
     * Tampilkan Dashboard Khusus Administrator.
     */
    public function dashboard(): View
    {
        $reports = $this->getReports();
        $totalReports = count($reports);
        $pendingReports = count(array_filter($reports, fn ($r) => $r['status'] === 'Pending'));
        $processReports = count(array_filter($reports, fn ($r) => $r['status'] === 'Diproses'));
        $doneReports = count(array_filter($reports, fn ($r) => $r['status'] === 'Selesai'));

        return view('admin.dashboard', compact(
            'reports',
            'totalReports',
            'pendingReports',
            'processReports',
            'doneReports'
        ));
    }

    /**
     * Perbarui status pengaduan oleh administrator.
     */
    public function updateStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'report_id' => ['required', 'string'],
            'status' => ['required', 'string', 'in:Pending,Diproses,Selesai,Ditolak'],
            'response' => ['nullable', 'string', 'max:500'],
        ]);

        return back()->with('success', "Status pengaduan {$validated['report_id']} berhasil diperbarui menjadi {$validated['status']}!");
    }
}
