<?php

namespace App\Policies;

use App\Models\Tugas;
use App\Models\User;

class TugasPolicy
{
    /**
     * Semua pengguna login dapat melihat daftar tugas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Semua pengguna login dapat melihat detail tugas.
     */
    public function view(User $user, Tugas $tugas): bool
    {
        return true;
    }

    /**
     * Hanya ketua tingkat yang boleh menambah tugas.
     */
    public function create(User $user): bool
    {
        return $user->isKetuaTingkat();
    }

    /**
     * Hanya ketua tingkat yang menginput yang boleh mengubah tugasnya.
     */
    public function update(User $user, Tugas $tugas): bool
    {
        return $user->isKetuaTingkat() && $user->id === $tugas->user_id;
    }

    /**
     * Hanya ketua tingkat yang menginput yang boleh menghapus tugasnya.
     */
    public function delete(User $user, Tugas $tugas): bool
    {
        return $this->update($user, $tugas);
    }

    /**
     * Semua pengguna login dapat menandai tugas selesai untuk dirinya sendiri.
     */
    public function tandaiSelesai(User $user, Tugas $tugas): bool
    {
        return true;
    }
}
