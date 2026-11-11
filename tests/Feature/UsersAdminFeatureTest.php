<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersAdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_search_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create([
            'name' => 'Another Administrator',
            'role' => 'admin',
        ]);
        $student = User::factory()->create([
            'name' => 'Cantika Mahasiswa',
            'nim' => '25170018',
            'email' => 'cantika@kampus.ac.id',
        ]);
        User::factory()->create(['name' => 'Another Student']);

        $this->actingAs($admin)->get(route('admin.users.index', ['search' => '25170018']))
            ->assertOk()
            ->assertViewIs('users.index')
            ->assertSee($student->name)
            ->assertSee($student->email)
            ->assertDontSee('Another Administrator')
            ->assertDontSee('Another Student');
    }

    public function test_admin_accounts_are_excluded_from_user_management_actions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $anotherAdmin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewHas('users', fn ($users): bool => $users->getCollection()
                ->every(fn (User $user): bool => ! $user->isAdmin()));

        $this->get(route('admin.users.edit', $anotherAdmin))->assertNotFound();
        $this->put(route('admin.users.update', $anotherAdmin), [
            'name' => 'Changed Admin',
            'nim' => $anotherAdmin->nim,
            'email' => $anotherAdmin->email,
        ])->assertNotFound();
        $this->delete(route('admin.users.destroy', $anotherAdmin))->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $anotherAdmin->id,
            'role' => 'admin',
        ]);
    }

    public function test_admin_user_directory_orders_students_by_nim_ascending(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $higherNimStudent = User::factory()->create(['nim' => '25170024']);
        $lowerNimStudent = User::factory()->create(['nim' => '25170023']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewHas('users', fn ($users): bool => $users->getCollection()
                ->pluck('nim')
                ->all() === [$lowerNimStudent->nim, $higherNimStudent->nim]);
    }

    public function test_admin_sidebar_shows_administration_role_and_user_management(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('id="portal-sidebar"', false)
            ->assertSee('ADMINISTRASI')
            ->assertSee('Administrator')
            ->assertSee('Manajemen Pengguna')
            ->assertDontSee('<span class="nav-label">Profil Saya</span>', false)
            ->assertSee('Pusat kendali kampus');
    }

    public function test_admin_dashboard_shows_user_and_complaint_pie_chart_totals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create();
        User::factory()->create();

        foreach (['Menunggu', 'Diproses', 'Selesai', 'Ditolak'] as $status) {
            Complaint::factory()->for($student)->create(['status' => $status]);
        }

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Total Pengguna')
            ->assertSee('3 akun')
            ->assertSee('Total Pengaduan')
            ->assertSee('4 pengaduan')
            ->assertDontSee('Pengaduan terbaru')
            ->assertViewHas('userPieChart', fn (array $chart): bool => $chart['total'] === 3
                && $chart['slices'][0]['value'] === 2
                && $chart['slices'][1]['value'] === 1)
            ->assertViewHas('complaintPieChart', fn (array $chart): bool => $chart['total'] === 4
                && array_column($chart['slices'], 'value') === [1, 1, 1, 1]);
    }

    public function test_student_sidebar_shows_student_menu_without_admin_links(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('RUANG MAHASISWA')
            ->assertSee('Buat Pengaduan')
            ->assertSee('Riwayat Pengaduan')
            ->assertDontSee('Ubah Kata Sandi')
            ->assertDontSee('<span class="nav-label">Profil Saya</span>', false)
            ->assertSee('Suaramu berarti')
            ->assertDontSee('Manajemen Pengguna');
    }

    public function test_student_cannot_access_user_management(): void
    {
        $student = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($student)->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.users.edit', $otherUser))->assertForbidden();
        $this->put(route('admin.users.update', $otherUser), [
            'name' => 'Changed Name',
            'nim' => $otherUser->nim,
            'email' => $otherUser->email,
        ])->assertForbidden();
        $this->delete(route('admin.users.destroy', $otherUser))->assertForbidden();
    }

    public function test_legacy_users_route_remains_available_to_admins_only(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/users')
            ->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/users')
            ->assertOk()
            ->assertViewIs('users.index');
    }

    public function test_admin_can_update_user_details_without_changing_their_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $student), [
            'name' => 'Updated Student',
            'nim' => '25170019',
            'email' => $student->email,
            'role' => 'admin',
        ])->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'Data pengguna berhasil diperbarui.');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'name' => 'Updated Student',
            'nim' => '25170019',
            'email' => $student->email,
            'role' => 'student',
        ]);
    }

    public function test_admin_cannot_change_a_student_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($admin)->from(route('admin.users.edit', $student))
            ->put(route('admin.users.update', $student), [
                'name' => $student->name,
                'nim' => $student->nim,
                'email' => $otherUser->email,
            ])
            ->assertRedirect(route('admin.users.edit', $student))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseHas('users', ['id' => $student->id, 'email' => $student->email]);
    }

    public function test_admin_can_delete_a_student_and_keep_their_complaints(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create();
        $complaint = Complaint::factory()->for($student)->create();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $student))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'Pengguna berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $student->id]);
        $this->assertDatabaseHas('complaints', ['id' => $complaint->id, 'user_id' => null]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->from(route('admin.users.index'))
            ->delete(route('admin.users.destroy', $admin))
            ->assertNotFound();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_delete_another_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $anotherAdmin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $anotherAdmin))
            ->assertNotFound();

        $this->assertDatabaseHas('users', ['id' => $anotherAdmin->id]);
    }
}
