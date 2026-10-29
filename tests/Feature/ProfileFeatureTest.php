<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_their_profile_page(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->get(route('profile.edit'))
            ->assertOk()
            ->assertViewIs('profile.edit')
            ->assertSee('Profil Saya')
            ->assertSee($student->name)
            ->assertSee('id="name" value="'.$student->name.'" maxlength="255" autocomplete="name" readonly', false)
            ->assertSee('id="nim" value="'.$student->nim.'" maxlength="32" autocomplete="username" readonly', false)
            ->assertDontSee('name="name"', false)
            ->assertDontSee('name="nim"', false)
            ->assertDontSee('<script', false)
            ->assertSee('profile_photo');
    }

    public function test_admin_can_view_their_profile_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Administrator')
            ->assertSee($admin->name)
            ->assertDontSee('name="nim"', false)
            ->assertDontSee('for="nim"', false);
    }

    public function test_guest_is_redirected_from_profile_page(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }

    public function test_student_can_update_profile_and_upload_a_photo_that_persists(): void
    {
        Storage::fake('public');
        $student = User::factory()->create([
            'name' => 'Original Student',
            'nim' => '25170019',
        ]);

        $this->actingAs($student)->put(route('profile.update'), [
            'name' => 'Updated Student',
            'nim' => '25170019',
            'email' => 'updated@kampus.ac.id',
            'profile_photo' => $this->validPngUpload(),
        ])->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'Profil berhasil diperbarui.');

        $student->refresh();
        $this->assertSame('Original Student', $student->name);
        $this->assertSame('25170019', $student->nim);
        $this->assertSame('updated@kampus.ac.id', $student->email);
        $this->assertNotNull($student->profile_photo_path);
        Storage::disk('public')->assertExists($student->profile_photo_path);

        $this->post(route('logout'));
        $this->post(route('login.store'), [
            'identity' => '25170019',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($student);
        $this->get(route('profile.edit'))
            ->assertOk()
            ->assertSee(route('profile.photo'), false);

        $this->get(route('profile.photo'))
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
    }

    public function test_student_cannot_change_name_or_nim_from_the_profile_update_request(): void
    {
        $student = User::factory()->create([
            'name' => 'Original Student',
            'nim' => '25170019',
        ]);

        $this->actingAs($student)->put(route('profile.update'), [
            'name' => 'Changed Name',
            'nim' => '25170023',
            'email' => 'updated@kampus.ac.id',
        ])->assertRedirect(route('profile.edit'));

        $student->refresh();
        $this->assertSame('Original Student', $student->name);
        $this->assertSame('25170019', $student->nim);
        $this->assertSame('updated@kampus.ac.id', $student->email);
    }

    public function test_admin_can_upload_a_profile_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('profile.update'), [
            'name' => 'Updated Campus Admin',
            'email' => $admin->email,
            'profile_photo' => $this->validPngUpload(),
        ])->assertRedirect(route('profile.edit'));

        $admin->refresh();
        $this->assertSame('Updated Campus Admin', $admin->name);
        $this->assertNotNull($admin->profile_photo_path);
        Storage::disk('public')->assertExists($admin->profile_photo_path);
    }

    public function test_admin_nim_is_unchanged_when_updating_profile(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $originalNim = $admin->nim;

        $this->actingAs($admin)->put(route('profile.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
        ])->assertRedirect(route('profile.edit'));

        $this->assertSame($originalNim, $admin->refresh()->nim);
    }

    public function test_invalid_profile_photo_is_rejected_without_changing_profile(): void
    {
        Storage::fake('public');
        $student = User::factory()->create();

        $this->actingAs($student)->from(route('profile.edit'))->put(route('profile.update'), [
            'name' => 'Should Not Be Saved',
            'nim' => $student->nim,
            'email' => $student->email,
            'profile_photo' => UploadedFile::fake()->createWithContent('not-an-image.txt', 'not an image'),
        ])->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('profile_photo');

        $this->assertSame($student->name, $student->refresh()->name);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_submitted_photo_path_cannot_be_used_to_overwrite_profile_photo_path(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->put(route('profile.update'), [
            'name' => $student->name,
            'nim' => $student->nim,
            'email' => $student->email,
            'profile_photo_path' => '../private/other-user.png',
        ])->assertRedirect(route('profile.edit'));

        $this->assertNull($student->refresh()->profile_photo_path);
    }

    public function test_replacing_a_profile_photo_removes_the_previous_file(): void
    {
        Storage::fake('public');
        $student = User::factory()->create();
        $previousPhotoPath = 'profile-photos/previous.png';
        Storage::disk('public')->put($previousPhotoPath, 'previous photo');
        $student->update(['profile_photo_path' => $previousPhotoPath]);

        $this->actingAs($student)->put(route('profile.update'), [
            'name' => $student->name,
            'nim' => $student->nim,
            'email' => $student->email,
            'profile_photo' => $this->validPngUpload(),
        ])->assertRedirect(route('profile.edit'));

        $student->refresh();
        $this->assertNotSame($previousPhotoPath, $student->profile_photo_path);
        Storage::disk('public')->assertMissing($previousPhotoPath);
        Storage::disk('public')->assertExists($student->profile_photo_path);
    }

    private function validPngUpload(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'profile.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l2cAAAAASUVORK5CYII='),
        );
    }
}
