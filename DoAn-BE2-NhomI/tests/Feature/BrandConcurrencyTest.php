<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Brand;
use App\Models\User;

class BrandConcurrencyTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create or find an admin user for authentication
        $this->admin = User::first();
        if (!$this->admin) {
            $this->admin = User::create([
                'name' => 'Test Admin',
                'email' => 'testadmin@example.com',
                'password' => bcrypt('password'),
            ]);
        }
    }

    /**
     * Test: Viewing a deleted brand redirects to index with error message.
     */
    public function test_viewing_deleted_brand_redirects_to_index_with_error(): void
    {
        $nonExistentId = 999999;

        $response = $this->actingAs($this->admin)->get(route('admin.brands.show', $nonExistentId));

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Test: Editing a deleted brand redirects to index with error message.
     */
    public function test_editing_deleted_brand_redirects_to_index_with_error(): void
    {
        $nonExistentId = 999999;

        $response = $this->actingAs($this->admin)->get(route('admin.brands.edit', $nonExistentId));

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Test: Updating a deleted brand redirects to index with error message.
     */
    public function test_updating_deleted_brand_redirects_to_index_with_error(): void
    {
        $nonExistentId = 999999;

        $response = $this->actingAs($this->admin)->put(route('admin.brands.update', $nonExistentId), [
            'name' => 'Updated Brand',
            'slug' => 'updated-brand',
        ]);

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Test: Deleting a deleted brand redirects to index with error message.
     */
    public function test_deleting_deleted_brand_redirects_to_index_with_error(): void
    {
        $nonExistentId = 999999;

        $response = $this->actingAs($this->admin)->delete(route('admin.brands.destroy', $nonExistentId));

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Test: Toggling status of a deleted brand redirects to index with error message.
     */
    public function test_toggling_status_of_deleted_brand_redirects_to_index_with_error(): void
    {
        $nonExistentId = 999999;

        $response = $this->actingAs($this->admin)->patch(route('admin.brands.toggleStatus', $nonExistentId));

        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Test: Creating a brand with logo_url longer than 255 chars fails validation.
     */
    public function test_creating_brand_with_too_long_logo_url_fails_validation(): void
    {
        $longUrl = 'https://example.com/' . str_repeat('a', 250);

        $response = $this->actingAs($this->admin)->post(route('admin.brands.store'), [
            'name' => 'Test Brand',
            'slug' => 'test-brand-long-url-' . time(),
            'logo_url' => $longUrl,
            'description' => 'Test description',
        ]);

        $response->assertSessionHasErrors('logo_url');
    }

    /**
     * Test: Updating a brand with logo_url longer than 255 chars fails validation.
     */
    public function test_updating_brand_with_too_long_logo_url_fails_validation(): void
    {
        // Create a brand first
        $brand = Brand::create([
            'name' => 'Existing Brand',
            'slug' => 'existing-brand-' . time(),
            'logo_url' => null,
            'description' => 'Test',
            'is_active' => 1,
        ]);

        $longUrl = 'https://example.com/' . str_repeat('b', 250);

        $response = $this->actingAs($this->admin)->put(route('admin.brands.update', $brand->brand_id), [
            'name' => 'Updated Brand',
            'slug' => $brand->slug,
            'logo_url' => $longUrl,
            'description' => 'Updated description',
        ]);

        $response->assertSessionHasErrors('logo_url');
    }
}

