<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PermissionConcurrencyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_concurrency_error_is_thrown_when_two_users_edit_permissions_at_the_same_time()
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

        // 2. Find or create a target user to edit permissions
        $targetUser = User::where('role', 'staff')->first();
        if (!$targetUser) {
            $targetUser = User::create([
                'full_name' => 'Test Staff',
                'email' => 'teststaff@example.com',
                'password_hash' => bcrypt('password'),
                'role' => 'staff',
                'is_active' => true,
                'is_verified' => true,
            ]);
        }

        // Set an older timestamp first
        $targetUser->updated_at = now()->subMinutes(5);
        $targetUser->save();

        $originalTime = $targetUser->fresh()->updated_at->format('Y-m-d H:i:s');

        // 3. Simulated concurrent change (User B updates targetUser updated_at)
        $targetUser->updated_at = now();
        $targetUser->save();

        $newTime = $targetUser->fresh()->updated_at->format('Y-m-d H:i:s');
        $this->assertNotEquals($originalTime, $newTime);

        // 4. User A tries to update using original time
        $payload = [
            'role' => 'staff',
            'permissions' => ['products.view', 'products.create'],
            'last_updated_at' => $originalTime,
        ];

        $response = $this->put(route('admin.permissions.update', $targetUser->user_id), $payload);

        // 5. Assert redirect back with error in session
        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Xung đột dữ liệu', session('error'));
    }

    public function test_successful_permission_update_when_no_concurrency_conflict()
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

        // 2. Find or create a target user to edit permissions
        $targetUser = User::where('role', 'staff')->first();
        if (!$targetUser) {
            $targetUser = User::create([
                'full_name' => 'Test Staff',
                'email' => 'teststaff@example.com',
                'password_hash' => bcrypt('password'),
                'role' => 'staff',
                'is_active' => true,
                'is_verified' => true,
            ]);
        }

        $targetUser->updated_at = now()->subMinutes(5);
        $targetUser->save();

        $originalTime = $targetUser->fresh()->updated_at->format('Y-m-d H:i:s');

        // 3. User submits update request with matching timestamp
        $payload = [
            'role' => 'staff',
            'permissions' => ['products.view'],
            'last_updated_at' => $originalTime,
        ];

        $response = $this->put(route('admin.permissions.update', $targetUser->user_id), $payload);

        // 4. Assert redirect back to index with success message
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.permissions.index'));
        $response->assertSessionHas('success');
    }

    public function test_permission_update_redirects_when_user_has_been_deleted()
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

        // 2. Submit update request for non-existent user ID
        $payload = [
            'role' => 'staff',
            'permissions' => ['products.view'],
            'last_updated_at' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $this->put(route('admin.permissions.update', 999999), $payload);

        // 3. Assert redirect to index with error message
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.permissions.index'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('không còn tồn tại', session('error'));
    }
}
