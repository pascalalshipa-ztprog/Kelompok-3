<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintReportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_a_structured_excel_compatible_csv_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'name' => 'Cantika Mahasiswa',
            'nim' => '25170018',
            'email' => 'cantika@kampus.ac.id',
        ]);
        $category = Category::factory()->create(['name' => 'Fasilitas']);
        $complaint = Complaint::factory()
            ->for($student)
            ->for($category)
            ->create([
                'title' => '=SUM(1,1)',
                'description' => 'Lampu koridor; sudah padam.',
                'location' => 'Gedung B',
            ]);

        $response = $this->actingAs($admin)->get(route('admin.complaints.export'));

        $response->assertDownload()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);

        $rows = array_map(
            fn (string $line): array => str_getcsv(rtrim($line, "\r"), ';', '"', ''),
            explode("\n", substr($content, 3)),
        );

        $this->assertSame([
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
        ], $rows[0]);
        $this->assertSame('SK-'.str_pad((string) $complaint->id, 6, '0', STR_PAD_LEFT), $rows[1][0]);
        $this->assertSame('Cantika Mahasiswa', $rows[1][1]);
        $this->assertSame('25170018', $rows[1][2]);
        $this->assertSame('Fasilitas', $rows[1][4]);
        $this->assertSame("'=SUM(1,1)", $rows[1][5]);
        $this->assertSame('Lampu koridor; sudah padam.', $rows[1][6]);
        $this->assertSame('Menunggu', $rows[1][8]);
    }

    public function test_student_cannot_download_the_admin_complaint_report(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.complaints.export'))
            ->assertForbidden();
    }

    public function test_admin_can_download_a_pdf_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['name' => 'Cantika Mahasiswa']);
        $category = Category::factory()->create(['name' => 'Fasilitas']);
        Complaint::factory()
            ->for($student)
            ->for($category)
            ->create([
                'title' => 'Lampu koridor padam',
                'description' => 'Lampu di koridor gedung B tidak menyala.',
                'status' => 'Diproses',
            ]);

        $response = $this->actingAs($admin)->get(route('admin.complaints.export-pdf'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('laporan-pengaduan-', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_student_cannot_download_the_admin_pdf_report(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.complaints.export-pdf'))
            ->assertForbidden();
    }

    public function test_pdf_report_view_includes_explanation_chart_and_workflow_diagram(): void
    {
        $html = view('reports.complaints-pdf', [
            'complaints' => collect(),
            'statusCounts' => [
                'Menunggu' => 2,
                'Diproses' => 1,
                'Selesai' => 3,
                'Ditolak' => 0,
            ],
            'totalCount' => 6,
            'generatedAt' => now(),
        ])->render();

        $this->assertStringContainsString('Penjelasan ringkasan', $html);
        $this->assertStringContainsString('Grafik pengaduan berdasarkan status', $html);
        $this->assertStringContainsString('Diagram alur penanganan', $html);
        $this->assertStringContainsString('Mahasiswa', $html);
        $this->assertStringContainsString('Admin', $html);
    }
}
