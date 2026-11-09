<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_the_complaint_api(): void
    {
        $this->getJson(route('api.complaints.index'))
            ->assertUnauthorized();
    }

    public function test_student_receives_only_their_complaints_with_resource_fields(): void
    {
        $student = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Fasilitas']);
        $ownComplaint = Complaint::factory()
            ->for($student)
            ->for($category)
            ->create(['title' => 'Lampu koridor padam']);
        $otherComplaint = Complaint::factory()->create(['title' => 'Aduan mahasiswa lain']);

        $this->actingAs($student)
            ->getJson(route('api.complaints.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'ticket_number',
                        'title',
                        'description',
                        'location',
                        'status',
                        'category',
                        'reporter',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonPath('data.0.id', $ownComplaint->id)
            ->assertJsonPath('data.0.ticket_number', 'SK-' . str_pad((string) $ownComplaint->id, 6, '0', STR_PAD_LEFT))
            ->assertJsonPath('data.0.category', $category->name)
            ->assertJsonPath('data.0.reporter', $student->name)
            ->assertJsonMissing(['title' => $otherComplaint->title]);
    }

    public function test_admin_receives_all_complaints_and_can_search_by_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $facilityCategory = Category::factory()->create(['name' => 'Fasilitas']);
        $cleanlinessCategory = Category::factory()->create(['name' => 'Kebersihan']);
        $facilityComplaint = Complaint::factory()
            ->for($facilityCategory)
            ->create(['title' => 'Lampu ruang kelas']);
        $cleanlinessComplaint = Complaint::factory()
            ->for($cleanlinessCategory)
            ->create(['title' => 'Lantai ruang kelas']);

        $this->actingAs($admin)
            ->getJson(route('api.complaints.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['title' => $facilityComplaint->title])
            ->assertJsonFragment(['title' => $cleanlinessComplaint->title]);

        $this->actingAs($admin)
            ->getJson(route('api.complaints.index', ['search' => 'Kebersihan']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $cleanlinessComplaint->id)
            ->assertJsonMissing(['title' => $facilityComplaint->title]);
    }

    public function test_complaint_api_returns_ten_items_per_page(): void
    {
        $student = User::factory()->create();
        $category = Category::factory()->create();
        Complaint::factory()
            ->count(11)
            ->for($student)
            ->for($category)
            ->create();

        $this->actingAs($student)
            ->getJson(route('api.complaints.index'))
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 11)
            ->assertJsonPath('meta.current_page', 1);
    }
}
