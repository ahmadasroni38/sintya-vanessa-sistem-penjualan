<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'date_of_birth',
        'phone_number',
        'address',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
    ];

    /**
     * Get the roles associated with the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withPivot('is_active', 'assigned_at', 'expires_at')->withTimestamps();
    }

    /**
     * Get the permissions directly assigned to the user.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')->withTimestamps();
    }

    /**
     * Get the active role for the user.
     */
    public function activeRole()
    {
        return $this->roles()->wherePivot('is_active', true)->first();
    }

    /**
     * Set the active role for the user.
     */
    public function setActiveRole(Role $role): void
    {
        // Deactivate all roles
        $this->roles()->updateExistingPivot($this->roles->pluck('id'), ['is_active' => false]);

        // Activate the specified role
        $this->roles()->updateExistingPivot($role->id, ['is_active' => true]);
    }

    /**
     * Check if the user has a specific permission through roles or direct assignment.
     */
    public function hasPermission(string $permission): bool
    {
        // Check direct permissions
        if ($this->permissions()->where('name', $permission)->exists()) {
            return true;
        }

        // Check permissions through active role
        $activeRole = $this->activeRole();
        if ($activeRole && $activeRole->hasPermission($permission)) {
            return true;
        }

        return false;
    }

    /**
     * Get all permissions for the user (from roles and direct).
     */
    public function getAllPermissions()
    {
        $permissions = collect();

        // Add direct permissions
        $permissions = $permissions->merge($this->permissions);

        // Add permissions from active role
        $activeRole = $this->activeRole();
        if ($activeRole) {
            $permissions = $permissions->merge($activeRole->permissions);
        }

        return $permissions->unique('id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Activate the user.
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Deactivate the user.
     */
    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    /**
     * Toggle the user's status.
     */
    public function toggleStatus(): void
    {
        $this->update(['status' => $this->isActive() ? 'inactive' : 'active']);
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
