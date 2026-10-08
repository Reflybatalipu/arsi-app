<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Default kolom role agar instance yang belum tersimpan tetap terbaca.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'role' => 'mahasiswa',
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
            'role' => Role::class,
        ];
    }

    public function isKetuaTingkat(): bool
    {
        return $this->role === Role::KetuaTingkat;
    }

    /**
     * Tugas yang diinput oleh pengguna ini (ketua tingkat).
     *
     * @return HasMany<Tugas>
     */
    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class);
    }

    /**
     * Seluruh tugas yang sudah ditandai selesai oleh pengguna ini.
     *
     * @return BelongsToMany<Tugas>
     */
    public function tugasSelesai(): BelongsToMany
    {
        return $this->belongsToMany(Tugas::class, 'tugas_selesai')->withTimestamps();
    }

    /**
     * Agenda milik pribadi pengguna ini.
     *
     * @return HasMany<Agenda>
     */
    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class);
    }
}
