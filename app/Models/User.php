<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    public function hasPermission(string $permissionSlug): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionSlug) {
                $query->where('slug', $permissionSlug);
            })
            ->exists();
    }

    /**
     * Assign a role to the user by role ID.
     * 
     * Note: This method should be called via UserRepository::assignRole() 
     * which handles the role lookup via RoleRepository and validation.
     *
     * @param int $roleId
     * @return void
     */
    public function assignRoleById(int $roleId): void
    {
        $this->roles()->attach($roleId);
    }

    /**
     * Remove a role from the user by role ID.
     * 
     * Note: This method should be called via UserRepository::removeRole() 
     * which handles the role lookup via RoleRepository.
     *
     * @param int $roleId
     * @return void
     */
    public function removeRoleById(int $roleId): void
    {
        $this->roles()->detach($roleId);
    }
}

