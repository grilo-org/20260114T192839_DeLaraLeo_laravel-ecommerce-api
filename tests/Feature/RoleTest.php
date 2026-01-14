<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Role $adminRole;
    private Role $userRole;
    private Permission $permission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Full system access',
        ]);

        $this->userRole = Role::create([
            'name' => 'User',
            'slug' => 'user',
            'description' => 'Regular user',
        ]);

        $this->permission = Permission::create([
            'name' => 'Manage Products',
            'slug' => 'products.manage',
            'description' => 'Manage products',
        ]);

        $this->user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->user->roles()->attach($this->adminRole->id);
    }

    public function test_user_can_get_roles(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_user_can_assign_role_to_user(): void
    {
        $newUser = User::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/roles/assign-to-user', [
                'user_id' => $newUser->id,
                'role_slug' => $this->userRole->slug,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Role assigned successfully.',
            ]);

        $this->assertTrue($newUser->hasRole('user'));
    }

    public function test_user_can_remove_role_from_user(): void
    {
        $newUser = User::factory()->create();
        $newUser->roles()->attach($this->userRole->id);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/roles/remove-from-user', [
                'user_id' => $newUser->id,
                'role_slug' => $this->userRole->slug,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Role removed successfully.',
            ]);

        $this->assertFalse($newUser->hasRole('user'));
    }

    public function test_user_can_assign_permission_to_role(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/roles/assign-permission', [
                'role_slug' => $this->userRole->slug,
                'permission_slug' => $this->permission->slug,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Permission assigned to role successfully.',
            ]);

        $this->assertTrue($this->userRole->hasPermission($this->permission->slug));
    }

    public function test_user_can_remove_permission_from_role(): void
    {
        $this->userRole->permissions()->attach($this->permission->id);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/roles/remove-permission', [
                'role_slug' => $this->userRole->slug,
                'permission_slug' => $this->permission->slug,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Permission removed from role successfully.',
            ]);

        $this->assertFalse($this->userRole->hasPermission($this->permission->slug));
    }

    public function test_user_has_permission_through_role(): void
    {
        $this->adminRole->permissions()->attach($this->permission->id);

        $this->assertTrue($this->user->hasPermission($this->permission->slug));
    }

    public function test_user_can_access_me_endpoint(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'roles',
            ]);
    }
}

