<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Services\RoleHierarchy;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'position',
        'karyawan_id',
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

    /**
     * Relasi ke data Karyawan jika user adalah karyawan.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Izin akses ke panel admin Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(RoleHierarchy::roles());
    }

    /**
     * Sinkronisasi otomatis field `position` ke Spatie Role sesuai materi Tugas.txt Section booted().
     */
    protected static function booted(): void
    {
        static::created(function ($user) {
            if (! empty($user->position) && Role::where('name', $user->position)->exists()) {
                $user->assignRole($user->position);
            }
        });

        static::updated(function ($user) {
            if ($user->isDirty('position')) {
                $pos = $user->position;
                if ($pos && Role::where('name', $pos)->exists()) {
                    $user->syncRoles([$pos]);
                } else {
                    $user->syncRoles([]);
                }
            }
        });
    }
}
