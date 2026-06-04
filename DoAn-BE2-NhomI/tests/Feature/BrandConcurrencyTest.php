<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BrandConcurrencyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_concurrency_error_is_thrown_when_two_users_edit_brand_at_the_same_time()
    {
        $this->withoutMiddleware();

        // 1. Authenticate as admin user
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'full_name' => 'Test Admin',
                'email' => 'testadmin@example.com',
                'password_hash' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
                'is_verified' => true,
            ]);
        }
        $this->actingAs($admin);

        // 2. Find or create a test brand
        $brand = Brand::first();
        if (!$brand) {
            $brand = Brand::create([
                'name' => 'Test Brand',
                'slug' => 'test-brand',
                'is_active' => true,
            ]);
        }

        // Set or ensure updated_at is populated
        $brand->updated_at = now()->subMinutes(5);
        $brand->save();

        $originalIsoTime = $brand->fresh()->updated_at->toIso8601String();

        // 3. User B updates the brand concurrently in the background
        $brand->updated_at = now();
        $brand->save();

        $newIsoTime = $brand->fresh()->updated_at->toIso8601String();
        $this->assertNotEquals($originalIsoTime, $newIsoTime);

        // 4. User A tries to update using original time
        $payload = [
            'name' => $brand->name . ' Updated A',
            'slug' => $brand->slug . '-updated-a',
            'last_updated_at' => $originalIsoTime,
        ];

        // 5. Submit request
        $response = $this->put(route('admin.brands.update', $brand->brand_id), $payload);

        // 6. Assert redirect back with concurrency error
        $response->assertStatus(302);
        $response->assertSessionHasErrors('concurrency_error');
    }

    public function test_successful_brand_update_when_no_concurrency_conflict()
    {
        $this->withoutMiddleware();

        // 1. Authenticate as admin
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'full_name' => 'Test Admin',
                'email' => 'testadmin@example.com',
                'password_hash' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
                'is_verified' => true,
            ]);
        }
        $this->actingAs($admin);

        // 2. Find or create a test brand
        $brand = Brand::first();
        if (!$brand) {
            $brand = Brand::create([
                'name' => 'Test Brand',
                'slug' => 'test-brand',
                'is_active' => true,
            ]);
        }

        $brand->updated_at = now()->subMinutes(5);
        $brand->save();

        $isoTime = $brand->fresh()->updated_at->toIso8601String();

        $payload = [
            'name' => $brand->name . ' Updated B',
            'slug' => $brand->slug . '-updated-b',
            'last_updated_at' => $isoTime,
        ];

        // 3. Submit request
        $response = $this->put(route('admin.brands.update', $brand->brand_id), $payload);

        // 4. Assert successful redirect to index with success message
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHasNoErrors();
        $this->assertEquals($brand->name . ' Updated B', $brand->fresh()->name);
    }

    public function test_concurrency_error_is_thrown_when_brand_has_been_deleted()
    {
        $this->withoutMiddleware();

        // 1. Authenticate as admin
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'full_name' => 'Test Admin',
                'email' => 'testadmin@example.com',
                'password_hash' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
                'is_verified' => true,
            ]);
        }
        $this->actingAs($admin);

        // 2. Submit update request for non-existent brand ID
        $payload = [
            'name' => 'Non Existent Brand',
            'slug' => 'non-existent-brand',
            'last_updated_at' => now()->toIso8601String(),
        ];

        $response = $this->put(route('admin.brands.update', 999999), $payload);

        // 3. Assert redirect back to brand index with deleted error message
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.brands.index'));
        $response->assertSessionHasErrors('concurrency_error');
    }
}
