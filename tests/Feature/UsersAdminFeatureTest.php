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
        $student = User::factory()->create([
            'name' => 'Cantika Mahasiswa',
            'nim' => '25170018',
            'email' => 'cantika@kampus.ac.id',
        ]);
        User::factory()->create(['name' => 'Another Student']);

        $this->actingAs($admin)->get(route('users.index', ['search' => '25170018']))
            ->assertOk()
            ->assertViewIs('users.index')
            ->assertSee($student->name)
            ->assertSee($student->email)
            ->assertDontSee('Another Student');
    }

    public function test_admin_sidebar_shows_administration_role_and_user_management(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="portal-sidebar"', false)
            ->assertSee('ADMINISTRASI')
            ->assertSee('Administrator')
            ->assertSee('Manajemen Pengguna')
            ->assertSee('Pusat kendali kampus');
    }

    public function test_student_sidebar_shows_student_menu_without_admin_links(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('RUANG MAHASISWA')
            ->assertSee('Buat Pengaduan')
            ->assertSee('Ubah Kata Sandi')
            ->assertSee('Riwayat Pengaduan')
            ->assertSee('Suaramu berarti')
            ->assertDontSee('Manajemen Pengguna');
    }

    public function test_student_cannot_access_user_management(): void
    {
        $student = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($student)->get(route('users.index'))->assertForbidden();
        $this->get(route('users.edit', $otherUser))->assertForbidden();
        $this->put(route('users.update', $otherUser), [
            'name' => 'Changed Name',
            'nim' => $otherUser->nim,
            'email' => $otherUser->email,
        ])->assertForbidden();
        $this->delete(route('users.destroy', $otherUser))->assertForbidden();
    }

    public function test_admin_can_update_user_details_without_changing_their_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create();

        $this->actingAs($admin)->put(route('users.update', $student), [
            'name' => 'Updated Student',
            'nim' => '25170019',
            'email' => 'updated@kampus.ac.id',
            'role' => 'admin',
        ])->assertRedirect(route('users.index'))
            ->assertSessionHas('status', 'Data pengguna berhasil diperbarui.');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'name' => 'Updated Student',
            'nim' => '25170019',
            'email' => 'updated@kampus.ac.id',
            'role' => 'student',
        ]);
    }

    public function test_admin_cannot_update_a_user_with_an_existing_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($admin)->from(route('users.edit', $student))
            ->put(route('users.update', $student), [
                'name' => $student->name,
                'nim' => $student->nim,
                'email' => $otherUser->email,
            ])
            ->assertRedirect(route('users.edit', $student))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseHas('users', ['id' => $student->id, 'email' => $student->email]);
    }

    public function test_admin_can_delete_a_student_and_keep_their_complaints(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create();
        $complaint = Complaint::factory()->for($student)->create();

        $this->actingAs($admin)->delete(route('users.destroy', $student))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('status', 'Pengguna berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $student->id]);
        $this->assertDatabaseHas('complaints', ['id' => $complaint->id, 'user_id' => null]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->from(route('users.index'))
            ->delete(route('users.destroy', $admin))
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_another_admin_when_an_admin_remains(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $anotherAdmin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->delete(route('users.destroy', $anotherAdmin))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', ['id' => $anotherAdmin->id]);
    }
}
