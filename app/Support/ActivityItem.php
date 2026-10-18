<?php

namespace App\Support;

use App\Enums\Priority;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Satu entri di dashboard: tugas atau agenda, lengkap dengan
 * tenggat terhitung, prioritas, dan status notifikasi.
 */
final class ActivityItem
{
    public const TYPE_TUGAS = 'tugas';

    public const TYPE_AGENDA = 'agenda';

    /**
     * @param  Model  $model  model Tugas atau Agenda asli
     * @param  string  $type  salah satu konstanta TYPE_*
     * @param  CarbonInterface|null  $deadline  waktu batas efektif (gabungan tanggal & waktu)
     * @param  bool  $selesai  status penyelesaian milik pengguna saat ini (khusus tugas)
     */
    public function __construct(
        public readonly Model $model,
        public readonly string $type,
        public readonly ?CarbonInterface $deadline,
        public readonly bool $selesai = false,
    ) {}

    public function isTugas(): bool
    {
        return $this->type === self::TYPE_TUGAS;
    }

    public function judul(): string
    {
        return (string) $this->model->getAttribute('judul');
    }

    /**
     * Prioritas hanya berlaku untuk tugas; agenda bernilai null.
     */
    public function priority(): ?Priority
    {
        if (! $this->isTugas() || $this->deadline === null) {
            return null;
        }

        return Priority::secondsUntil($this->deadline);
    }

    /**
     * Sisa waktu dalam detik (negatif berarti sudah lewat).
     */
    public function sisaDetik(): ?int
    {
        if ($this->deadline === null) {
            return null;
        }

        return $this->deadline->getTimestamp() - now()->getTimestamp();
    }

    /**
     * Apakah tugas belum selesai dan mendekati/lewat tenggat (<= 24 jam)?
     */
    public function perluPengingatTugas(): bool
    {
        $sisa = $this->sisaDetik();

        return $this->isTugas() && ! $this->selesai && $sisa !== null && $sisa <= 24 * 3600;
    }

    /**
     * Apakah agenda sedang mendekati waktu pelaksanaan (<= 5 jam)?
     */
    public function perluPengingatAgenda(): bool
    {
        $sisa = $this->sisaDetik();

        return ! $this->isTugas()
            && $sisa !== null
            && $sisa <= 5 * 3600
            && $sisa > -6 * 3600;
    }

    /**
     * Sisa waktu dalam format ramah ("3 jam lagi", "2 hari", "Telat 1 hari").
     */
    public function sisaWaktu(): string
    {
        $sisa = $this->sisaDetik();

        if ($sisa === null) {
            return 'Tanpa tenggat';
        }

        $telat = $sisa < 0;
        $sisa = abs($sisa);

        $units = [
            86400 => 'hari',
            3600 => 'jam',
            60 => 'menit',
        ];

        foreach ($units as $seconds => $label) {
            if ($sisa >= $seconds) {
                $nilai = intdiv($sisa, $seconds);

                return $telat
                    ? "Telat {$nilai} {$label}"
                    : "{$nilai} {$label} lagi";
            }
        }

        return $telat ? 'Tenggat baru saja lewat' : 'Kurang dari 1 menit';
    }
}
