<?php

namespace App\Policies;

use App\Models\Agenda;
use App\Models\User;

class AgendaPolicy
{
    /**
     * Semua pengguna login dapat melihat daftar agenda miliknya sendiri.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Agenda bersifat pribadi: hanya pemilik yang boleh melihat.
     */
    public function view(User $user, Agenda $agenda): bool
    {
        return $user->id === $agenda->user_id;
    }

    /**
     * Semua pengguna login dapat membuat agenda.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Hanya pemilik yang boleh mengubah agenda.
     */
    public function update(User $user, Agenda $agenda): bool
    {
        return $user->id === $agenda->user_id;
    }

    /**
     * Hanya pemilik yang boleh menghapus agenda.
     */
    public function delete(User $user, Agenda $agenda): bool
    {
        return $user->id === $agenda->user_id;
    }
}
