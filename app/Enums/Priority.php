<?php

namespace App\Enums;

use Carbon\CarbonInterface;

enum Priority: string
{
    case Critical = 'kritis';

    case High = 'tinggi';

    case Medium = 'sedang';

    case Low = 'rendah';

    /**
     * Label yang ditampilkan di UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Critical => 'Kritis',
            self::High => 'Tinggi',
            self::Medium => 'Sedang',
            self::Low => 'Rendah',
        };
    }

    /**
     * Nilai untuk keperluan pengurutan (makin kecil makin mendesak).
     */
    public function order(): int
    {
        return match ($this) {
            self::Critical => 0,
            self::High => 1,
            self::Medium => 2,
            self::Low => 3,
        };
    }

    /**
     * Klasifikasi prioritas berdasarkan jarak waktu dari sekarang.
     */
    public static function secondsUntil(CarbonInterface $deadline): self
    {
        $seconds = $deadline->getTimestamp() - now()->getTimestamp();

        return match (true) {
            $seconds <= 0 => self::Critical,
            $seconds <= 24 * 3600 => self::Critical,
            $seconds <= 3 * 86400 => self::High,
            $seconds <= 7 * 86400 => self::Medium,
            default => self::Low,
        };
    }
}
