<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'karyawan_id',
        'gudang_id',
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

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($role): bool
    {
        return $this->role && $this->role->nama_role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->nama_role, $roles);
    }

    public function karyawan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function gudang(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    public function gudangId(): ?int
    {
        if ($this->hasRole('super_admin')) {
            return null;
        }
        return $this->gudang_id;
    }

    public function kandangIds(): array
    {
        if ($this->hasRole('super_admin')) {
            return Kandang::pluck('id')->toArray();
        }

        if ($this->karyawan_id) {
            return $this->karyawan->kandang()
                ->wherePivot('is_active', true)
                ->pluck('kandang_id')
                ->toArray();
        }

        return [];
    }

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
}
