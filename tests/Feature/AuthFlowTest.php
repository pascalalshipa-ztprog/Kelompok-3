<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_the_login_page(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_login_page_is_rendered(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertViewIs('auth.login')
            ->assertDontSee('<script', false)
            ->assertSee('NIM: 25170023 · Kata sandi: password')
            ->assertSee('Email: admin@kampus.ac.id · Kata sandi: password');
    }

    public function test_login_page_links_to_password_recovery(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('password.request'), false);
    }

    public function test_password_recovery_page_is_rendered(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertViewIs('auth.forgot-password');
    }

    public function test_password_recovery_sends_a_reset_notification_to_the_registered_user(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'cantika@kampus.ac.id']);

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHas('status', 'Jika email terdaftar, tautan untuk mengatur ulang kata sandi akan dikirim.');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_recovery_does_not_disclose_unknown_email_addresses(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'unknown@kampus.ac.id'])
            ->assertRedirect()
            ->assertSessionHas('status', 'Jika email terdaftar, tautan untuk mengatur ulang kata sandi akan dikirim.');

        Notification::assertNothingSent();
    }

    public function test_valid_reset_token_changes_password_and_returns_to_login(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertViewIs('auth.reset-password')
            ->assertSee($user->email);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-password-123', $user->refresh()->password));
    }

    public function test_invalid_reset_token_does_not_change_password(): void
    {
        $user = User::factory()->create(['password' => 'current-password']);

        $this->from(route('password.reset', ['token' => 'invalid-token', 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => 'invalid-token',
                'email' => $user->email,
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertRedirect(route('password.reset', ['token' => 'invalid-token', 'email' => $user->email]))
            ->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('current-password', $user->refresh()->password));
    }

    public function test_register_page_is_rendered(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertViewIs('auth.register')
            ->assertDontSee('<script', false);
    }

    public function test_password_change_form_is_available_from_sidebar_but_not_dashboard(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('dashboard.password.edit'), false)
            ->assertDontSee('Simpan kata sandi')
            ->assertDontSee('id="ubah-kata-sandi"', false);

        $this->get(route('dashboard.password.edit'))
            ->assertOk()
            ->assertViewIs('dashboard.password')
            ->assertSee(route('dashboard.password.update'), false)
            ->assertSee('Ubah kata sandi');

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('dashboard.password.edit'))
            ->assertForbidden();
    }

    public function test_student_can_change_password_with_their_current_password(): void
    {
        $student = User::factory()->create(['password' => 'current-password']);

        $this->actingAs($student)->put(route('dashboard.password.update'), [
            'current_password' => 'current-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('dashboard.password.edit'))
            ->assertSessionHas('status', 'Kata sandi berhasil diperbarui.');

        $this->assertTrue(Hash::check('new-password-123', $student->refresh()->password));
    }

    public function test_student_cannot_change_password_without_the_correct_current_password(): void
    {
        $student = User::factory()->create(['password' => 'current-password']);

        $this->actingAs($student)->from(route('dashboard.password.edit'))->put(route('dashboard.password.update'), [
            'current_password' => 'incorrect-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('dashboard.password.edit'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('current-password', $student->refresh()->password));
    }

    public function test_admin_cannot_use_student_dashboard_password_change(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('dashboard.password.update'), [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertForbidden();
    }

    public function test_student_registration_creates_a_hashed_student_account(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Cantika Dwi Utari',
            'nim' => '25170018',
            'email' => 'cantika@kampus.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'name' => 'Cantika Dwi Utari',
            'nim' => '25170018',
            'email' => 'cantika@kampus.ac.id',
            'role' => 'student',
        ]);
        $this->assertNotSame('password123', User::query()->where('nim', '25170018')->value('password'));
    }

    public function test_registration_rejects_an_existing_nim(): void
    {
        User::factory()->create(['nim' => '25170018']);

        $this->from(route('register'))
            ->post(route('register.store'), $this->registrationData(['nim' => '25170018']))
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('nim');
    }

    public function test_registration_rejects_an_existing_email(): void
    {
        User::factory()->create(['email' => 'cantika@kampus.ac.id']);

        $this->from(route('register'))
            ->post(route('register.store'), $this->registrationData(['email' => 'cantika@kampus.ac.id']))
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');
    }

    public function test_login_accepts_a_student_nim(): void
    {
        $student = User::factory()->create(['nim' => '25170018', 'password' => 'password123']);

        $this->post(route('login.store'), ['identity' => '25170018', 'password' => 'password123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($student);
    }

    public function test_login_accepts_an_admin_email(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@kampus.ac.id',
            'role' => 'admin',
            'password' => 'password123',
        ]);

        $this->post(route('login.store'), ['identity' => 'admin@kampus.ac.id', 'password' => 'password123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_rejects_an_invalid_password(): void
    {
        User::factory()->create(['nim' => '25170018']);

        $this->from(route('login'))
            ->post(route('login.store'), ['identity' => '25170018', 'password' => 'incorrect'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('identity');
    }

    public function test_login_is_locked_for_two_minutes_after_three_failed_attempts(): void
    {
        $this->freezeTime();

        $student = User::factory()->create([
            'nim' => '25170018',
            'email' => 'student@kampus.ac.id',
            'password' => 'correct-password',
        ]);
        $failedAttempts = [
            ['identity' => $student->nim, 'password' => 'incorrect-password'],
            ['identity' => $student->email, 'password' => 'incorrect-password'],
            ['identity' => $student->nim, 'password' => 'incorrect-password'],
        ];

        foreach (array_slice($failedAttempts, 0, 2) as $loginData) {
            $this->from(route('login'))->post(route('login.store'), $loginData)
                ->assertRedirect(route('login'))
                ->assertSessionHasErrors([
                    'identity' => 'NIM/email atau kata sandi tidak sesuai.',
                ]);

            $this->travel(30)->seconds();
        }

        $this->from(route('login'))->post(route('login.store'), $failedAttempts[2])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'identity' => 'Terlalu banyak percobaan masuk. Silakan coba lagi dalam 2 menit.',
            ]);

        $this->followingRedirects()->from(route('login'))->post(route('login.store'), $failedAttempts[2])
            ->assertOk()
            ->assertSee('Terlalu banyak percobaan masuk. Silakan coba lagi dalam 2 menit.');

        $this->travel(119)->seconds();

        $this->from(route('login'))->post(route('login.store'), [
            'identity' => $student->email,
            'password' => 'correct-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'identity' => 'Terlalu banyak percobaan masuk. Silakan coba lagi dalam 1 menit.',
            ]);

        $this->travel(1)->seconds();

        $this->post(route('login.store'), [
            'identity' => $student->email,
            'password' => 'correct-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($student);
    }

    public function test_portal_uses_server_side_session_expiration_without_javascript(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="portal-logout-form"', false)
            ->assertSee('id="portal-sidebar"', false)
            ->assertDontSee('<script', false);

        $this->assertSame(5, config('session.lifetime'));
    }

    public function test_guest_is_redirected_from_the_complaint_list(): void
    {
        $this->get(route('complaints.index'))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_the_complaint_api(): void
    {
        $this->getJson(route('api.complaints.index'))->assertUnauthorized();
    }

    public function test_student_can_create_a_complaint(): void
    {
        $student = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Fasilitas', 'slug' => 'fasilitas']);

        $response = $this->actingAs($student)->post(route('complaints.store'), [
            'category_id' => $category->id,
            'title' => 'Lampu koridor padam',
            'description' => 'Lampu koridor gedung B sudah padam sejak minggu lalu.',
            'location' => 'Gedung B lantai 2',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('complaints', [
            'user_id' => $student->id,
            'category_id' => $category->id,
            'title' => 'Lampu koridor padam',
            'status' => 'Menunggu',
        ]);
    }

    public function test_student_cannot_create_a_complaint_with_an_invalid_category(): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('complaints.create'))
            ->post(route('complaints.store'), [
                'category_id' => 99999,
                'title' => 'Lampu koridor padam',
                'description' => 'Lampu koridor gedung B sudah padam sejak minggu lalu.',
            ])
            ->assertRedirect(route('complaints.create'))
            ->assertSessionHasErrors('category_id');
    }

    public function test_student_can_view_only_their_own_complaints(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        $category = Category::factory()->create();
        $ownComplaint = Complaint::factory()->for($student)->for($category)->create();
        $otherComplaint = Complaint::factory()->for($otherStudent)->for($category)->create();

        $this->actingAs($student)->get(route('complaints.index'))
            ->assertOk()
            ->assertSee($ownComplaint->title)
            ->assertDontSee($otherComplaint->title);

        $this->get(route('complaints.show', $otherComplaint))->assertForbidden();
    }

    public function test_student_can_edit_own_pending_complaint_without_changing_status(): void
    {
        $student = User::factory()->create();
        $category = Category::factory()->create();
        $complaint = Complaint::factory()->for($student)->for($category)->create(['status' => 'Menunggu']);

        $this->actingAs($student)->put(route('complaints.update', $complaint), [
            'category_id' => $category->id,
            'title' => 'Judul pengaduan telah diperbarui',
            'description' => 'Keterangan lengkap yang diperbarui untuk pengaduan ini.',
            'status' => 'Selesai',
        ])->assertRedirect(route('complaints.show', $complaint));

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'title' => 'Judul pengaduan telah diperbarui',
            'status' => 'Menunggu',
        ]);
    }

    public function test_student_cannot_edit_another_students_complaint(): void
    {
        $student = User::factory()->create();
        $complaint = Complaint::factory()->create();

        $this->actingAs($student)->get(route('complaints.edit', $complaint))->assertForbidden();
    }

    public function test_student_cannot_edit_a_complaint_after_processing_started(): void
    {
        $student = User::factory()->create();
        $complaint = Complaint::factory()->for($student)->create(['status' => 'Diproses']);

        $this->actingAs($student)->get(route('complaints.edit', $complaint))->assertForbidden();
    }

    public function test_student_can_delete_their_own_pending_complaint(): void
    {
        $student = User::factory()->create();
        $complaint = Complaint::factory()->for($student)->create(['status' => 'Menunggu']);

        $this->actingAs($student)->delete(route('complaints.destroy', $complaint))
            ->assertRedirect(route('complaints.index'));

        $this->assertDatabaseMissing('complaints', ['id' => $complaint->id]);
    }

    public function test_admin_can_view_all_complaints_and_update_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $firstStudent = User::factory()->create();
        $secondStudent = User::factory()->create();
        $category = Category::factory()->create();
        $firstComplaint = Complaint::factory()->for($firstStudent)->for($category)->create();
        $secondComplaint = Complaint::factory()->for($secondStudent)->for($category)->create();

        $this->actingAs($admin)->get(route('complaints.index'))
            ->assertOk()
            ->assertSee($firstComplaint->title)
            ->assertSee($secondComplaint->title);

        $this->put(route('complaints.update', $firstComplaint), [
            'category_id' => $category->id,
            'title' => $firstComplaint->title,
            'description' => $firstComplaint->description,
            'status' => 'Diproses',
        ])->assertRedirect(route('complaints.show', $firstComplaint));

        $this->assertDatabaseHas('complaints', ['id' => $firstComplaint->id, 'status' => 'Diproses']);
    }

    public function test_admin_can_update_status_inline_and_return_to_the_same_list_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $complaint = Complaint::factory()->create(['status' => 'Menunggu']);

        $this->actingAs($admin)
            ->from(route('complaints.index', ['search' => 'Lampu', 'page' => 2]))
            ->patch(route('complaints.update-status', $complaint), ['status' => 'Diproses'])
            ->assertRedirect(route('complaints.index', ['search' => 'Lampu', 'page' => 2]))
            ->assertSessionHas('status', 'Status pengaduan berhasil diperbarui.');

        $this->assertDatabaseHas('complaints', ['id' => $complaint->id, 'status' => 'Diproses']);
    }

    public function test_admin_sees_inline_status_controls_in_the_complaint_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $complaint = Complaint::factory()->create();

        $this->actingAs($admin)->get(route('complaints.index'))
            ->assertOk()
            ->assertSee('class="inline-status-form"', false)
            ->assertSee('<th>Perbarui</th>', false)
            ->assertSee(route('complaints.update-status', $complaint), false);
    }

    public function test_student_list_does_not_render_inline_status_controls(): void
    {
        $student = User::factory()->create();
        Complaint::factory()->for($student)->create();

        $this->actingAs($student)->get(route('complaints.index'))
            ->assertOk()
            ->assertDontSee('inline-status-form');
    }

    public function test_student_cannot_update_complaint_status_inline(): void
    {
        $student = User::factory()->create();
        $complaint = Complaint::factory()->for($student)->create(['status' => 'Menunggu']);

        $this->actingAs($student)
            ->patch(route('complaints.update-status', $complaint), ['status' => 'Selesai'])
            ->assertForbidden();

        $this->assertDatabaseHas('complaints', ['id' => $complaint->id, 'status' => 'Menunggu']);
    }

    public function test_complaint_search_matches_title_and_category(): void
    {
        $student = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Fasilitas', 'slug' => 'fasilitas']);
        $match = Complaint::factory()->for($student)->for($category)->create(['title' => 'Perbaikan proyektor kelas']);
        $other = Complaint::factory()->for($student)->for(Category::factory())->create(['title' => 'Jadwal konsultasi akademik']);

        $this->actingAs($student)->get(route('complaints.index', ['search' => 'proyektor']))
            ->assertOk()
            ->assertSee($match->title)
            ->assertDontSee($other->title);
    }

    public function test_complaint_api_returns_only_the_students_own_records(): void
    {
        $student = User::factory()->create();
        $ownComplaint = Complaint::factory()->for($student)->create(['title' => 'Private student complaint']);
        Complaint::factory()->create(['title' => 'Another student complaint']);

        $this->actingAs($student)->getJson(route('api.complaints.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', $ownComplaint->title);
    }

    public function test_complaint_list_is_paginated(): void
    {
        $student = User::factory()->create();
        $category = Category::factory()->create();
        $titles = array_map(
            fn (int $number): array => ['title' => 'Pagination report '.$number],
            range(1, 11),
        );
        Complaint::factory()->count(11)->for($student)->for($category)->sequence(...$titles)->create();

        $response = $this->actingAs($student)->get(route('complaints.index', ['page' => 2]));

        $response->assertOk()->assertSee('Pagination report 1');
        $response->assertViewHas('complaints', fn ($complaints): bool => $complaints->perPage() === 10 && $complaints->count() === 1);
    }

    /** @return array<string, string> */
    private function registrationData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Cantika Dwi Utari',
            'nim' => '25170018',
            'email' => 'cantika@kampus.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }
}
