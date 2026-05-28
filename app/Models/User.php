<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    public const SUPER_ADMIN_EMAIL = 'geilerelias@gmail.com';

    public const ROLES = ['trabajador', 'cliente', 'admin', 'superadmin'];

    public const DEFAULT_PERMISSIONS = [
        'cliente' => [
            'contact.ai',
            'dashboard.view',
        ],
        'trabajador' => [
            'dashboard.view',
            'employee.requests.create',
            'employee.requests.view-own',
            'appointments.create',
            'events.view',
            'events.register',
        ],
        'admin' => [
            'dashboard.view',
            'users.manage',
            'roles.manage',
            'permissions.manage',
            'forms.manage',
            'employee.requests.manage',
            'appointments.manage',
            'events.manage',
            'news.manage',
            'payments.manage',
            'tools.manage',
        ],
        'superadmin' => [
            '*',
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $appends = [
        'effective_role',
        'effective_permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getEffectiveRoleAttribute(): string
    {
        if ($this->isSuperAdmin()) {
            return 'superadmin';
        }

        return $this->getRoleNames()->first() ?: 'cliente';
    }

    public function getEffectivePermissionsAttribute(): array
    {
        if ($this->isSuperAdmin()) {
            return ['*'];
        }

        return $this->getAllPermissions()->pluck('name')->values()->all();
    }

    public function isSuperAdmin(): bool
    {
        return strtolower($this->email) === self::SUPER_ADMIN_EMAIL;
    }

    public function canAccess(string $permission): bool
    {
        return $this->isSuperAdmin() || $this->hasPermissionTo($permission);
    }
}
