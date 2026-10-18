<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Support\ActivityItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Beranda: gabungan tugas & agenda pengguna, sudah diurutkan
     * berdasarkan rekomendasi pengerjaan (tenggat paling dekat lebih dulu;
     * pada waktu yang sama, tugas selalu didahulukan).
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $tugasSelesaiIds = $user->tugasSelesai()->pluck('tugas_id');

        $tugasItems = Tugas::query()
            ->with('user:id,name')
            ->get()
            ->map(fn (Tugas $tugas) => new ActivityItem(
                $tugas,
                ActivityItem::TYPE_TUGAS,
                $tugas->deadlineAt(),
                $tugasSelesaiIds->contains($tugas->id),
            ));

        $agendaItems = $user->agendas()
            ->get()
            ->map(fn ($agenda) => new ActivityItem(
                $agenda,
                ActivityItem::TYPE_AGENDA,
                $agenda->waktuMulai(),
            ));

        $items = $this->urutkanRekomendasi($tugasItems->concat($agendaItems));

        $notifikasi = $items
            ->filter(fn (ActivityItem $item) => $item->perluPengingatTugas()
                || $item->perluPengingatAgenda()
                || $item->perluPengingatSelesaiAgenda())
            ->values();

        $rekomendasi = $items
            ->filter(fn (ActivityItem $item) => $item->isTugas()
                ? (! $item->selesai && $item->deadline !== null)
                : ! $item->selesaiOtomatis())
            ->take(5)
            ->values();

        $statistik = [
            'tugas_aktif' => $tugasItems->where('selesai', false)->count(),
            'tugas_selesai' => $tugasItems->where('selesai', true)->count(),
            'agenda_total' => $agendaItems->count(),
            'peringatan' => $notifikasi->count(),
        ];

        return view('beranda', compact('items', 'notifikasi', 'rekomendasi', 'statistik'));
    }

    /**
     * Urutkan berdasarkan tenggat menaik; jika tenggat sama,
     * tugas didahulukan atas agenda.
     *
     * @param  Collection<int, ActivityItem>  $items
     * @return Collection<int, ActivityItem>
     */
    private function urutkanRekomendasi($items)
    {
        return $items
            ->sort(function (ActivityItem $a, ActivityItem $b) {
                $ta = $a->deadline?->getTimestamp() ?? PHP_INT_MAX;
                $tb = $b->deadline?->getTimestamp() ?? PHP_INT_MAX;

                if ($ta !== $tb) {
                    return $ta <=> $tb;
                }

                // Tenggat sama: tugas tetap utama.
                return ($a->isTugas() ? 0 : 1) <=> ($b->isTugas() ? 0 : 1);
            })
            ->values();
    }
}
