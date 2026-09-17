<?php

namespace App\Models;

use App\Models\AuditLog;
use App\Models\HarvestAlert;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'kwt_id',
        'is_active',
        'email_verified_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function kwt()
    {
        return $this->belongsTo(Kwt::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    public function cultivationCyclesCreated()
    {
        return $this->hasMany(CultivationCycle::class, 'created_by');
    }

    public function cultivationCyclesUpdated()
    {
        return $this->hasMany(CultivationCycle::class, 'updated_by');
    }

    public function growthMonitorings()
    {
        return $this->hasMany(GrowthMonitoring::class, 'created_by');
    }

    public function harvestsCreated()
    {
        return $this->hasMany(Harvest::class, 'created_by');
    }

    public function harvestsVerified()
    {
        return $this->hasMany(Harvest::class, 'verified_by');
    }

    public function harvestAlerts()
    {
        return $this->hasMany(HarvestAlert::class, 'recipient_user_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()
            ->where('slug', $roleSlug)
            ->exists();
    }

    public function hasPermission(string $permissionSlug): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionSlug) {
                $query->where('slug', $permissionSlug);
            })
            ->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isOperatorKwt(): bool
    {
        return $this->hasRole('operator_kwt');
    }

    public function isKoperasi(): bool
    {
        return $this->hasRole('koperasi');
    }
}
