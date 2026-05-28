<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    public const SUPER_ADMIN_EMAIL = 'geilerelias@gmail.com';

    public const ROLES = ['trabajador', 'cliente', 'admin', 'superadmin'];

    /** @var array<string, string> */
    public const IDENTIFICATION_TYPES = [
        'cc' => 'Cedula de ciudadania',
        'ce' => 'Cedula de extranjeria',
        'ti' => 'Tarjeta de identidad',
        'ptp' => 'Permiso por Proteccion Temporal (PTP)',
        'pep' => 'Permiso Especial de Permanencia (PEP)',
        'pasaporte' => 'Pasaporte',
        'certificado_migracion' => 'Certificado Migracion Colombia',
        'otro' => 'Otro documento oficial',
    ];

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
        'identification_type',
        'identification_number',
        'phone',
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

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    /**
     * @return list<string>
     */
    public static function identificationTypeKeys(): array
    {
        return array_keys(self::IDENTIFICATION_TYPES);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function identificationTypesForSelect(): array
    {
        return collect(self::IDENTIFICATION_TYPES)
            ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }

    public function identificationTypeLabel(): string
    {
        if (! $this->identification_type) {
            return '';
        }

        return self::IDENTIFICATION_TYPES[$this->identification_type] ?? $this->identification_type;
    }

    /**
     * @return array<string, array<int, mixed|string>>
     */
    public static function identificationRules(Request $request): array
    {
        return [
            'identification_type' => ['required', 'string', Rule::in(self::identificationTypeKeys())],
            'identification_number' => [
                'required',
                'string',
                'min:5',
                'max:30',
                'regex:/^[A-Za-z0-9.\-\s]+$/',
                Rule::unique('users', 'identification_number')->where(
                    fn ($query) => $query->where('identification_type', $request->input('identification_type')),
                ),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function identificationMessages(): array
    {
        return [
            'identification_type.required' => 'Selecciona el tipo de identificacion.',
            'identification_type.in' => 'El tipo de identificacion no es valido.',
            'identification_number.required' => 'Ingresa el numero de identificacion.',
            'identification_number.regex' => 'El numero de identificacion solo puede incluir letras, numeros, puntos y guiones.',
            'identification_number.unique' => 'Esta identificacion ya esta registrada con el mismo tipo de documento.',
        ];
    }
}
