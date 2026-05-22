<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductConcurrencyTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected int $nonExistentProductId = 999999;

    protected function setUp(): void
    {
        parent::setUp();

        // Tắt VerifyCsrfToken middleware để tránh lỗi 419 trong test environment
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \App\Http\Middleware\VerifyCsrfToken::class
        ]);

        // Tạo user admin giả lập
        $this->adminUser = User::create([
            'email' => 'admin_test_' . uniqid() . '@example.com',
            'password_hash' => bcrypt('password'),
            'full_name' => 'Admin Test',
            'role' => 'admin',
            'is_active' => 1,
            'is_verified' => 1,
        ]);
    }

    /**
     * Test viewing a deleted or non-existent product.
     */
    public function test_viewing_deleted_product_redirects_to_index_with_error(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.products.show', $this->nonExistentProductId));

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('error', 'Sản phẩm không tồn tại hoặc đã bị xóa.');
    }

    /**
     * Test accessing edit page of a deleted or non-existent product.
     */
    public function test_editing_deleted_product_redirects_to_index_with_error(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.products.edit', $this->nonExistentProductId));

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('error', 'Sản phẩm không tồn tại hoặc đã bị xóa trước đó.');
    }

    /**
     * Test updating a deleted or non-existent product.
     */
    public function test_updating_deleted_product_redirects_to_index_with_error(): void
    {
        // Gửi request PUT update tới sản phẩm không tồn tại (giả lập bị người khác xóa)
        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.products.update', $this->nonExistentProductId), [
                'name' => 'Sản phẩm test update',
                'slug' => 'san-pham-test-update-' . uniqid(),
                'category_id' => 1, // Bất kỳ ID nào vì controller check sự tồn tại trước khi validate
                'brand_id' => 1,
                'base_price' => 100000,
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('error', 'Sản phẩm này đã bị xóa bởi người dùng khác hoặc không tồn tại.');
    }

    /**
     * Test deleting a deleted or non-existent product.
     */
    public function test_deleting_deleted_product_redirects_to_index_with_error(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.products.destroy', $this->nonExistentProductId));

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('error', 'Sản phẩm không tồn tại hoặc đã bị xóa trước đó.');
    }

    /**
     * Test validation rules for store action with too long image url.
     */
    public function test_creating_product_with_too_long_image_url_fails_validation(): void
    {
        $category = Category::create([
            'name' => 'Category Test ' . uniqid(),
            'slug' => 'cat-test-' . uniqid(),
            'is_active' => 1,
        ]);

        $brand = Brand::create([
            'name' => 'Brand Test ' . uniqid(),
            'slug' => 'brand-test-' . uniqid(),
            'is_active' => 1,
        ]);

        // Gửi URL quá dài (ví dụ: chuỗi lặp lại 300 ký tự)
        $longUrl = str_repeat('a', 300);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.products.store'), [
                'name' => 'Sản phẩm test validate URL',
                'slug' => 'san-pham-test-validate-url-' . uniqid(),
                'category_id' => $category->category_id,
                'brand_id' => $brand->brand_id,
                'base_price' => 100000,
                'images' => [
                    ['url' => $longUrl, 'is_primary' => 1]
                ]
            ]);

        $response->assertSessionHasErrors(['images.0.url']);
    }

    /**
     * Test validation rules for update action with too long image url.
     */
    public function test_updating_product_with_too_long_image_url_fails_validation(): void
    {
        $category = Category::create([
            'name' => 'Category Test ' . uniqid(),
            'slug' => 'cat-test-' . uniqid(),
            'is_active' => 1,
        ]);

        $brand = Brand::create([
            'name' => 'Brand Test ' . uniqid(),
            'slug' => 'brand-test-' . uniqid(),
            'is_active' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->category_id,
            'brand_id' => $brand->brand_id,
            'name' => 'Sản phẩm test',
            'slug' => 'san-pham-test-' . uniqid(),
            'base_price' => 100000,
            'is_active' => 1,
        ]);

        $longUrl = str_repeat('a', 300);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.products.update', $product->product_id), [
                'name' => 'Sản phẩm test',
                'slug' => $product->slug,
                'category_id' => $category->category_id,
                'brand_id' => $brand->brand_id,
                'base_price' => 100000,
                'images' => [
                    ['url' => $longUrl, 'is_primary' => 1]
                ]
            ]);

        $response->assertSessionHasErrors(['images.0.url']);
    }
}
