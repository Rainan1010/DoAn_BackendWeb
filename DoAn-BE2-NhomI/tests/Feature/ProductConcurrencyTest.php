<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductConcurrencyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_concurrency_error_is_thrown_when_two_users_edit_at_the_same_time()
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

        // 2. Find or create a test product
        $product = Product::first();
        if (!$product) {
            $this->markTestSkipped('No product available to test concurrency.');
        }

        // Ensure valid category and brand exist and are assigned to avoid validation errors
        $category = \App\Models\Category::first();
        if (!$category) {
            $category = \App\Models\Category::create(['name' => 'Test Category', 'slug' => 'test-category']);
        }
        $brand = \App\Models\Brand::first();
        if (!$brand) {
            $brand = \App\Models\Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        }

        $product->category_id = $category->category_id;
        $product->brand_id = $brand->brand_id;
        // Set or make sure updated_at is populated
        $product->updated_at = now()->subMinutes(5);
        $product->save();

        $originalIsoTime = $product->fresh()->updated_at->toIso8601String();

        // 3. User B updates the product in the background, changing the updated_at in DB
        $product->updated_at = now();
        $product->save();

        $newIsoTime = $product->fresh()->updated_at->toIso8601String();
        $this->assertNotEquals($originalIsoTime, $newIsoTime);

        // 4. User A (who opened the form earlier) tries to submit with the old/original time
        $payload = [
            'name' => $product->name,
            'slug' => $product->slug,
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'base_price' => $product->base_price,
            'last_updated_at' => $originalIsoTime, // Sending the old timestamp
        ];

        // 5. Submit the update request
        $response = $this->put(route('admin.products.update', $product->product_id), $payload);

        // 6. Assert redirect back with concurrency error in session
        $response->assertStatus(302);
        $response->assertSessionHasErrors('concurrency_error');
    }

    public function test_successful_update_when_no_concurrency_conflict()
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

        // 2. Find a test product
        $product = Product::first();
        if (!$product) {
            $this->markTestSkipped('No product available.');
        }

        // Ensure valid category and brand exist and are assigned to avoid validation errors
        $category = \App\Models\Category::first();
        if (!$category) {
            $category = \App\Models\Category::create(['name' => 'Test Category', 'slug' => 'test-category']);
        }
        $brand = \App\Models\Brand::first();
        if (!$brand) {
            $brand = \App\Models\Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        }

        $product->category_id = $category->category_id;
        $product->brand_id = $brand->brand_id;
        // Set or make sure updated_at is populated
        $product->updated_at = now()->subMinutes(5);
        $product->save();

        $isoTime = $product->fresh()->updated_at->toIso8601String();

        $payload = [
            'name' => $product->name . ' Updated',
            'slug' => $product->slug . '-updated',
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'base_price' => $product->base_price,
            'last_updated_at' => $isoTime, // Matching timestamp
        ];

        // 3. Submit the update request
        $response = $this->put(route('admin.products.update', $product->product_id), $payload);

        // 4. Assert successful redirect to show route
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.products.show', $product->product_id));
        $response->assertSessionHasNoErrors();
        
        $this->assertEquals($product->name . ' Updated', $product->fresh()->name);
    }

    public function test_concurrency_error_is_thrown_when_product_has_been_deleted()
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

        // 2. Submit the update request for a non-existent product ID (representing a deleted product)
        $payload = [
            'name' => 'Non Existent',
            'slug' => 'non-existent',
            'category_id' => 1,
            'brand_id' => 1,
            'base_price' => 1000,
        ];
        
        $response = $this->put(route('admin.products.update', 999999), $payload);

        // 3. Assert redirect back to index with deleted product concurrency error
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHasErrors('concurrency_error');
    }

    public function test_validation_fails_when_base_price_exceeds_maximum_limit()
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

        // 2. Find a product
        $product = Product::first();
        if (!$product) {
            $this->markTestSkipped('No product available.');
        }

        // 3. Submit update request with an out-of-range base_price (e.g. 2.790001E+28)
        $payload = [
            'name' => $product->name,
            'slug' => $product->slug,
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'base_price' => 2.790001E+28, // Out of range
            'last_updated_at' => $product->updated_at ? $product->updated_at->toIso8601String() : ($product->created_at ? $product->created_at->toIso8601String() : ''),
        ];

        $response = $this->put(route('admin.products.update', $product->product_id), $payload);

        // 4. Assert validation error for base_price
        $response->assertStatus(302);
        $response->assertSessionHasErrors('base_price');
    }
}
