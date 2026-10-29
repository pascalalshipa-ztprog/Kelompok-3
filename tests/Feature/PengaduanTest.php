<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PengaduanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pastikan akun awal telah tersedia.
     */
    protected function setUp(): void
    {
        parent::setUp();

        User::updateOrCreate(
            ['email' => 'mahasiswa@kampus.ac.id'],
            [
                'name' => 'Naufal Marzuki',
                'identifier' => '25170018',
                'role' => 'mahasiswa',
                'password' => Hash::make('mahasiswa123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@kampus.ac.id'],
            [
                'name' => 'Admin Kemahasiswaan & Sarpras',
                'identifier' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );
    }

    /**
     * Dashboard mahasiswa dapat diakses dengan sukses dan tombol outline riwayat tidak mepet.
     */
    public function test_dashboard_page_loads_successfully(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Selamat datang kembali, Naufal');
        $response->assertSee('Total Pengaduan');
        $response->assertSee('Sedang Diproses');
        $response->assertSee('Telah Selesai');
        $response->assertSee('Pengaduan Terbaru');
        $response->assertSee('outline-action-btn');
        $response->assertSee('Buka Riwayat Lengkap');
    }

    /**
     * Halaman formulir buat pengaduan dapat diakses dengan sukses.
     */
    public function test_create_pengaduan_page_loads_successfully(): void
    {
        $response = $this->get(route('pengaduan.create'));

        $response->assertStatus(200);
        $response->assertSee('Sampaikan Aspirasimu');
        $response->assertSee('Kategori Pengaduan');
        $response->assertSee('Fasilitas & Sarana Prasarana', false);
        $response->assertSee('Kirim Pengaduan');
    }

    /**
     * Halaman riwayat pengaduan dapat diakses dengan sukses.
     */
    public function test_history_pengaduan_page_loads_successfully(): void
    {
        $response = $this->get(route('pengaduan.riwayat'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Pengaduan');
        $response->assertSee('Unduh CSV');
        $response->assertSee('Semua Kategori');
        $response->assertSee('Semua Status');
    }

    /**
     * Pengiriman pengaduan dengan data valid berhasil dan dialihkan ke halaman riwayat.
     */
    public function test_store_pengaduan_validates_and_redirects_successfully(): void
    {
        $payload = [
            'kategori' => 'Fasilitas dan Sarana Prasarana',
            'judul' => 'AC Ruang Lab Jaringan Rusak',
            'lokasi' => 'Lab Komputer Gedung D',
            'description' => 'AC mengeluarkan hawa panas dan air menetes ke lantai ruang lab.',
        ];

        $response = $this->post(route('pengaduan.store'), $payload);

        $response->assertRedirect(route('pengaduan.riwayat'));
        $response->assertSessionHas('success');
    }

    /**
     * Pengiriman pengaduan dengan data tidak lengkap memunculkan validasi error.
     */
    public function test_store_pengaduan_validation_fails_on_empty_data(): void
    {
        $response = $this->post(route('pengaduan.store'), [
            'description' => 'pendek', // min 10
        ]);

        $response->assertSessionHasErrors(['kategori', 'description']);
    }

    /**
     * Halaman login dapat dimuat dan menampilkan form autentikasi.
     */
    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang');
        $response->assertSee('NIM / Email Kampus');
        $response->assertSee('Akun Mahasiswa');
        $response->assertSee('Akun Administrator');
    }

    /**
     * Mahasiswa berhasil login menggunakan NIM dan dialihkan ke dashboard.
     */
    public function test_mahasiswa_can_login_with_nim(): void
    {
        $response = $this->post(route('login.post'), [
            'identifier' => '25170018',
            'password' => 'mahasiswa123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertFalse(auth()->user()->isAdmin());
    }

    /**
     * Administrator berhasil login menggunakan Email dan dialihkan ke admin dashboard.
     */
    public function test_admin_can_login_with_email(): void
    {
        $response = $this->post(route('login.post'), [
            'identifier' => 'admin@kampus.ac.id',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isAdmin());
    }

    /**
     * Mahasiswa tidak dapat mengakses dashboard khusus administrator (403 Forbidden).
     */
    public function test_mahasiswa_cannot_access_admin_dashboard(): void
    {
        $student = User::where('role', 'mahasiswa')->first();
        $response = $this->actingAs($student)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    /**
     * Administrator dapat mengakses dashboard admin.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('PORTAL ADMINISTRATOR');
        $response->assertSee('Tren Laporan Masuk');
        $response->assertSee('Distribusi Berdasarkan Bidang');
    }

    /**
     * Pengguna dapat melakukan logout.
     */
    public function test_user_can_logout(): void
    {
        $student = User::where('role', 'mahasiswa')->first();
        $response = $this->actingAs($student)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
