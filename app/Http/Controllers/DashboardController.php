<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Beranda: ringkasan tugas & agenda milik pengguna.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $tugasTerdekat = Tugas::query()
            ->whereDate('tenggat', '>=', today())
            ->orderBy('tenggat')
            ->limit(5)
            ->get();

        $tugasSelesaiIds = $user->tugasSelesai()->pluck('tugas_id');

        $agendaTerdekat = $user->agendas()
            ->whereDate('tanggal', '>=', today())
            ->orderBy('tanggal')
            ->limit(5)
            ->get();

        $statistik = [
            'tugas_total' => Tugas::count(),
            'tugas_selesai' => $tugasSelesaiIds->count(),
            'agenda_total' => Agenda::where('user_id', $user->id)->count(),
        ];

        return view('beranda', compact('tugasTerdekat', 'agendaTerdekat', 'statistik', 'tugasSelesaiIds'));
    }
}
