<?php

namespace App\Models;

use Database\Factories\TugasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['user_id', 'judul', 'mata_kuliah', 'dosen', 'deskripsi', 'tenggat'])]
class Tugas extends Model
{
    /** @use HasFactory<TugasFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tenggat' => 'date',
        ];
    }

    /**
     * Ketua tingkat yang menginput tugas ini.
     *
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mahasiswa yang sudah menyelesaikan tugas ini.
     *
     * @return BelongsToMany<User>
     */
    public function diselesaikanOleh(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tugas_selesai')->withTimestamps();
    }

    public function isTenggatLewat(): bool
    {
        return $this->tenggat !== null && $this->tenggat->isPast();
    }
}
