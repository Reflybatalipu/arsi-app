<?php

namespace App\Enums;

enum Role: string
{
    case Mahasiswa = 'mahasiswa';

    case KetuaTingkat = 'ketua_tingkat';

    /**
     * Label yang ditampilkan di UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Mahasiswa => 'Mahasiswa',
            self::KetuaTingkat => 'Ketua Tingkat',
        };
    }
}
