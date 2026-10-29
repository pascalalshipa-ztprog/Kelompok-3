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

        $response = $this->actingAs($admin)->get(route('complaints.export'));

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
            ->get(route('complaints.export'))
            ->assertForbidden();
    }
}
