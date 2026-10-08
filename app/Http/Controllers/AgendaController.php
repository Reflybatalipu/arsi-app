<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgendaRequest;
use App\Models\Agenda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    /**
     * Daftar agenda milik pengguna saat ini.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Agenda::class);

        $agendas = $request->user()->agendas()
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->paginate(10);

        return view('agenda.index', compact('agendas'));
    }

    /**
     * Formulir tambah agenda.
     */
    public function create(): View
    {
        $this->authorize('create', Agenda::class);

        return view('agenda.create');
    }

    /**
     * Simpan agenda baru.
     */
    public function store(StoreAgendaRequest $request): RedirectResponse
    {
        $agenda = $request->user()->agendas()->create($request->validated());

        return redirect()->route('agenda.index')->with('success', 'Agenda berhasil ditambahkan.');
    }

    /**
     * Detail agenda.
     */
    public function show(Request $request, Agenda $agenda): View
    {
        $this->authorize('view', $agenda);

        return view('agenda.show', compact('agenda'));
    }

    /**
     * Formulir ubah agenda.
     */
    public function edit(Agenda $agenda): View
    {
        $this->authorize('update', $agenda);

        return view('agenda.edit', compact('agenda'));
    }

    /**
     * Perbarui agenda.
     */
    public function update(StoreAgendaRequest $request, Agenda $agenda): RedirectResponse
    {
        $this->authorize('update', $agenda);

        $agenda->update($request->validated());

        return redirect()->route('agenda.index')->with('success', 'Agenda berhasil diperbarui.');
    }

    /**
     * Hapus agenda.
     */
    public function destroy(Agenda $agenda): RedirectResponse
    {
        $this->authorize('delete', $agenda);

        $agenda->delete();

        return redirect()->route('agenda.index')->with('success', 'Agenda berhasil dihapus.');
    }
}
