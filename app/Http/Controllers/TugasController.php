<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTugasRequest;
use App\Models\Tugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TugasController extends Controller
{
    /**
     * Daftar seluruh tugas (terlihat oleh semua pengguna login).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Tugas::class);

        $tenggat = $request->query('tenggat');

        $tugas = Tugas::query()
            ->with('user:id,name')
            ->withCount('diselesaikanOleh')
            ->when($tenggat === 'lewat', fn ($query) => $query->whereDate('tenggat', '<', today()))
            ->when($tenggat === 'akan', fn ($query) => $query->whereDate('tenggat', '>=', today()))
            ->orderByRaw('tenggat IS NULL, tenggat ASC')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $tugasSelesaiIds = $request->user()
            ->tugasSelesai()
            ->pluck('tugas_id')
            ->all();

        return view('tugas.index', compact('tugas', 'tugasSelesaiIds'));
    }

    /**
     * Formulir tambah tugas (ketua tingkat).
     */
    public function create(): View
    {
        $this->authorize('create', Tugas::class);

        return view('tugas.create');
    }

    /**
     * Simpan tugas baru.
     */
    public function store(StoreTugasRequest $request): RedirectResponse
    {
        $tugas = $request->user()->tugas()->create($request->validated());

        return redirect()->route('tugas.show', $tugas)->with('success', 'Tugas berhasil ditambahkan.');
    }

    /**
     * Detail satu tugas.
     */
    public function show(Tugas $tugas): View
    {
        $this->authorize('view', $tugas);

        $tugas->load('user:id,name')->loadCount('diselesaikanOleh');

        $penyelesai = $tugas->diselesaikanOleh()->get(['users.id', 'users.name']);

        $sudahSelesai = $penyelesai->contains('id', auth()->id());

        return view('tugas.show', compact('tugas', 'penyelesai', 'sudahSelesai'));
    }

    /**
     * Formulir ubah tugas.
     */
    public function edit(Tugas $tugas): View
    {
        $this->authorize('update', $tugas);

        return view('tugas.edit', compact('tugas'));
    }

    /**
     * Perbarui tugas.
     */
    public function update(StoreTugasRequest $request, Tugas $tugas): RedirectResponse
    {
        $this->authorize('update', $tugas);

        $tugas->update($request->validated());

        return redirect()->route('tugas.show', $tugas)->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Hapus tugas.
     */
    public function destroy(Tugas $tugas): RedirectResponse
    {
        $this->authorize('delete', $tugas);

        $tugas->delete();

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dihapus.');
    }

    /**
     * Tandai / batalkan tugas selesai untuk pengguna saat ini.
     */
    public function tandaiSelesai(Request $request, Tugas $tugas): RedirectResponse
    {
        $this->authorize('tandaiSelesai', $tugas);

        $pivot = $tugas->diselesaikanOleh()->where('users.id', $request->user()->id);

        if ($pivot->exists()) {
            $pivot->detach();
            $message = 'Status selesai dibatalkan.';
        } else {
            $tugas->diselesaikanOleh()->attach($request->user()->id);
            $message = 'Tugas ditandai selesai.';
        }

        return back()->with('success', $message);
    }
}
