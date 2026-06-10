<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_BIOMEDICAL = 'biomedical';
    public const ROLE_MANAGER = 'manager';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    public static function roles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_BIOMEDICAL,
            self::ROLE_MANAGER,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isBiomedical(): bool
    {
        return $this->role === self::ROLE_BIOMEDICAL;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function canManageInstallations(): bool
    {
        return $this->isAdmin() || $this->isBiomedical();
    }

    public function canViewInstallationKpis(): bool
    {
        return $this->isAdmin() || $this->isBiomedical() || $this->isManager();
    }

    public function canViewStrategicInstallationKpis(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function installationPermissionSummary(): string
    {
        if ($this->isAdmin()) {
            return 'Accès complet: KPIs stratégiques, création, modification, transitions et archivage.';
        }

        if ($this->isBiomedical()) {
            return 'Accès opérationnel: création, modification et transitions. KPIs limités aux alertes terrain. Archivage interdit.';
        }

        if ($this->isManager()) {
            return 'Accès lecture/validation: KPIs stratégiques et consultation. Modification et archivage interdits.';
        }

        return 'Accès non défini.';
    }
}
