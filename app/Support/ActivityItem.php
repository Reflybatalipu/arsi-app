<?php

namespace App\Support;

use App\Enums\Priority;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Satu entri di dashboard: tugas atau agenda, lengkap dengan
 * tenggat terhitung, status, prioritas, dan aturan peringatan.
 *
 * Semua logika tampilan (status, label, teks pencarian) berada di sini
 * agar view tetap bersih dan mudah diuji.
 */
final class ActivityItem
{
    public const TYPE_TUGAS = 'tugas';

    public const TYPE_AGENDA = 'agenda';

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_LEWAT = 'lewat';

    /**
     * Agenda dianggap "mendekati waktu" jika kurang dari 5 jam lagi.
     */
    public const JENDELA_AGENDA_JAM = 5;

    /**
     * Tugas dianggap "mendekati tenggat" jika kurang dari 24 jam lagi.
     */
    public const JENDELA_TUGAS_JAM = 24;

    /**
     * Notifikasi "agenda selesai" tetap ditampilkan selama 24 jam.
     */
    public const JENDELA_SELESAI_AGENDA_JAM = 24;

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
     * Status tampilan.
     *
     * - Tugas: 'selesai' jika sudah ditandai pengguna, 'lewat' jika tenggat
     *   terlampaui, selain itu 'aktif'.
     * - Agenda: otomatis 'selesai' begitu waktu pelaksanaan terlampaui
     *   (tanpa aksi manual), selain itu 'aktif'.
     */
    public function status(): string
    {
        if ($this->isTugas()) {
            if ($this->selesai) {
                return self::STATUS_SELESAI;
            }

            return ($this->sisaDetik() ?? 1) < 0 ? self::STATUS_LEWAT : self::STATUS_AKTIF;
        }

        return ($this->sisaDetik() ?? 1) < 0 ? self::STATUS_SELESAI : self::STATUS_AKTIF;
    }

    /**
     * Label status untuk ditampilkan.
     */
    public function statusLabel(): string
    {
        return match ($this->status()) {
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_LEWAT => 'Lewat waktu',
            default => 'Aktif',
        };
    }

    /**
     * Apakah agenda ini sudah otomatis selesai karena waktu pelaksanaannya lewat?
     */
    public function selesaiOtomatis(): bool
    {
        return ! $this->isTugas() && ($this->sisaDetik() ?? 1) < 0;
    }

    /**
     * Apakah tugas belum selesai dan mendekati/lewat tenggat (<= 24 jam)?
     */
    public function perluPengingatTugas(): bool
    {
        $sisa = $this->sisaDetik();

        return $this->isTugas()
            && ! $this->selesai
            && $sisa !== null
            && $sisa <= self::JENDELA_TUGAS_JAM * 3600;
    }

    /**
     * Apakah agenda sedang mendekati waktu pelaksanaan (<= 5 jam)?
     */
    public function perluPengingatAgenda(): bool
    {
        $sisa = $this->sisaDetik();

        return ! $this->isTugas()
            && $sisa !== null
            && $sisa <= self::JENDELA_AGENDA_JAM * 3600
            && $sisa >= 0;
    }

    /**
     * Apakah agenda baru saja selesai otomatis (dalam 24 jam terakhir)?
     */
    public function perluPengingatSelesaiAgenda(): bool
    {
        $sisa = $this->sisaDetik();

        return ! $this->isTugas()
            && $sisa !== null
            && $sisa < 0
            && $sisa > -self::JENDELA_SELESAI_AGENDA_JAM * 3600;
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

    /**
     * Waktu efektif dalam format tampilan.
     */
    public function waktuLabel(): string
    {
        if ($this->deadline === null) {
            return 'Tanpa tenggat';
        }

        return $this->deadline->isoFormat('D MMM Y, HH:mm');
    }

    /**
     * Teks ringkas di bawah judul kartu.
     */
    public function subjudul(): string
    {
        if ($this->isTugas()) {
            $mataKuliah = (string) $this->model->getAttribute('mata_kuliah');
            $dosen = (string) $this->model->getAttribute('dosen');

            return trim(($mataKuliah !== '' ? $mataKuliah : 'Tanpa mata kuliah')
                .($dosen !== '' ? ' · '.$dosen : ''));
        }

        $jenis = (string) $this->model->getAttribute('jenis');
        $deskripsi = Str::limit((string) $this->model->getAttribute('deskripsi'), 60);

        return trim(($jenis !== '' ? ucfirst($jenis) : 'Agenda')
            .($deskripsi !== '' ? ' · '.$deskripsi : ''));
    }

    /**
     * Teks gabungan untuk pencarian realtime di sisi klien.
     */
    public function searchText(): string
    {
        $parts = [
            $this->judul(),
            $this->model->getAttribute('mata_kuliah'),
            $this->model->getAttribute('dosen'),
            $this->model->getAttribute('jenis'),
            $this->model->getAttribute('deskripsi'),
        ];

        return mb_strtolower(trim(implode(' ', array_map('strval', $parts))));
    }
}
