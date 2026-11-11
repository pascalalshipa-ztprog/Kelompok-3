<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComplaintReportController extends Controller
{
    private const STATUSES = ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'];

    public function export(Request $request): StreamedResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $complaints = Complaint::query()
            ->with(['user:id,name,nim,email', 'category:id,name'])
            ->orderBy('complaints.id')
            ->lazy(500);

        return response()->streamDownload(function () use ($complaints): void {
            $stream = fopen('php://output', 'wb');

            if ($stream === false) {
                throw new \RuntimeException('Unable to open the report output stream.');
            }

            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, [
                'ID Pengaduan',
                'Nama Pelapor',
                'NIM',
                'Email',
                'Kategori',
                'Judul',
                'Deskripsi',
                'Lokasi',
                'Status',
                'Tanggal Dibuat',
            ], ';', '"', '');

            foreach ($complaints as $complaint) {
                fputcsv($stream, [
                    'SK-'.str_pad((string) $complaint->id, 6, '0', STR_PAD_LEFT),
                    $this->spreadsheetSafeCell($complaint->user?->name ?? 'Akun dihapus'),
                    $this->spreadsheetSafeCell($complaint->user?->nim),
                    $this->spreadsheetSafeCell($complaint->user?->email),
                    $this->spreadsheetSafeCell($complaint->category->name),
                    $this->spreadsheetSafeCell($complaint->title),
                    $this->spreadsheetSafeCell($complaint->description),
                    $this->spreadsheetSafeCell($complaint->location),
                    $this->spreadsheetSafeCell($complaint->status),
                    $complaint->created_at->format('d/m/Y H:i'),
                ], ';', '"', '');
            }

            fclose($stream);
        }, 'laporan-pengaduan-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $statusCounts = array_fill_keys(self::STATUSES, 0);
        $groupedStatusCounts = Complaint::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        foreach (self::STATUSES as $status) {
            $statusCounts[$status] = (int) $groupedStatusCounts->get($status, 0);
        }

        $complaints = Complaint::query()
            ->with(['user:id,name,nim', 'category:id,name'])
            ->orderBy('complaints.id')
            ->get();

        return Pdf::loadView('reports.complaints-pdf', [
            'complaints' => $complaints,
            'statusCounts' => $statusCounts,
            'totalCount' => array_sum($statusCounts),
            'generatedAt' => now(),
        ])
            ->setPaper('a4', 'portrait')
            ->download('laporan-pengaduan-'.now()->format('Ymd-His').'.pdf');
    }

    private function spreadsheetSafeCell(?string $value): string
    {
        $value ??= '';

        if (preg_match('/^[\x00-\x20]*[=+\-@]/', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }
}
