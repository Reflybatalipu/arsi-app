@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

{{-- Header sapaan --}}
<div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Halo, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-gray-500">
            Kamu masuk sebagai <span class="font-medium text-gray-700">{{ auth()->user()->role->label() }}</span>.
            Atur waktumu antara tugas dan agenda hari ini.
        </p>
    </div>
    <a href="{{ route('tugas.index') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <x-fa name="list-check" /> Lihat Semua Tugas
    </a>
</div>

{{-- Kartu statistik --}}
<div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <x-stat-card label="Tugas Aktif" :value="$statistik['tugas_aktif']" tone="blue" />
    <x-stat-card label="Tugas Selesai" :value="$statistik['tugas_selesai']" tone="green" />
    <x-stat-card label="Agenda Saya" :value="$statistik['agenda_total']" tone="indigo" />
    <x-stat-card label="Peringatan" :value="$statistik['peringatan']" :tone="$statistik['peringatan'] > 0 ? 'red' : 'gray'" />
</div>

{{-- Panel notifikasi peringatan --}}
<section class="mb-8">
    <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-500">
        <x-fa name="bell" /> Peringatan
    </h2>

    @if ($notifikasi->isEmpty())
        <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <x-fa name="circle-check" />
            Tidak ada peringatan. Semua tugas dan agendamu masih aman.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($notifikasi as $item)
                <x-alert-item :item="$item" />
            @endforeach
        </div>
    @endif
</section>

{{-- Rekomendasi pengerjaan --}}
<section class="mb-8 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <h2 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-500">
            <x-fa name="bullseye" /> Rekomendasi Pengerjaan
        </h2>
        <span class="text-xs text-gray-400">Diurutkan dari tenggat terdekat · tugas didahulukan</span>
    </div>

    @if ($rekomendasi->isEmpty())
        <p class="text-sm text-gray-500">Belum ada tugas atau agenda aktif. Saatnya beristirahat sebentar.</p>
    @else
        <ol class="space-y-2">
            @foreach ($rekomendasi as $index => $item)
                <x-reco-row :item="$item" :index="$index" />
            @endforeach
        </ol>
    @endif
</section>

{{-- Toolbar pencarian & filter --}}
<div class="mb-8 rounded-xl border border-gray-200 bg-white p-3 shadow-sm sm:p-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <x-fa name="magnifying-glass"
                  class="pointer-events-none absolute inset-y-0 left-3 my-auto text-gray-400" />
            <input
                type="search"
                id="search"
                placeholder="Cari tugas atau agenda secara realtime..."
                autocomplete="off"
                class="w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            >
        </div>

        <div class="flex flex-wrap gap-2" id="filter-jenis" role="group" aria-label="Filter jenis">
            @foreach (['semua' => 'Semua', 'tugas' => 'Tugas', 'agenda' => 'Agenda'] as $value => $label)
                <button type="button" data-filter="jenis" data-value="{{ $value }}"
                        class="filter-chip rounded-full border px-3 py-1.5 text-xs font-medium"
                        aria-pressed="{{ $value === 'semua' ? 'true' : 'false' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-2" id="filter-status" role="group" aria-label="Filter status">
            @foreach (['semua' => 'Semua status', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'lewat' => 'Lewat waktu'] as $value => $label)
                <button type="button" data-filter="status" data-value="{{ $value }}"
                        class="filter-chip rounded-full border px-3 py-1.5 text-xs font-medium"
                        aria-pressed="{{ $value === 'semua' ? 'true' : 'false' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <p class="mt-2 hidden text-xs text-gray-400" id="hasil-pencarian" aria-live="polite"></p>
</div>

{{-- Kartu tugas & agenda --}}
<div id="daftar-item" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($items as $item)
        <x-item-card :item="$item" />
    @endforeach
</div>

<div id="kosong" class="hidden rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
    Tidak ada tugas atau agenda yang cocok dengan pencarian/filtermu.
</div>

@endsection

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const search = document.getElementById('search');
                const cards = Array.from(document.querySelectorAll('[data-item]'));
                const kosong = document.getElementById('kosong');
                const infoHasil = document.getElementById('hasil-pencarian');
                const chips = Array.from(document.querySelectorAll('.filter-chip'));

                if (!search && cards.length === 0) return;

                const filters = { jenis: 'semua', status: 'semua' };

                function terapkan() {
                    const q = (search?.value || '').trim().toLowerCase();
                    let tampil = 0;

                    for (const card of cards) {
                        const cocokJenis = filters.jenis === 'semua' || card.dataset.type === filters.jenis;
                        const cocokStatus = filters.status === 'semua' || card.dataset.status === filters.status;
                        const cocokCari = q === '' || (card.dataset.search || '').includes(q);
                        const terlihat = cocokJenis && cocokStatus && cocokCari;

                        card.classList.toggle('hidden', !terlihat);
                        if (terlihat) tampil++;
                    }

                    if (kosong) kosong.classList.toggle('hidden', tampil > 0);

                    if (infoHasil) {
                        const aktif = q || filters.jenis !== 'semua' || filters.status !== 'semua';
                        infoHasil.classList.toggle('hidden', !aktif);
                        infoHasil.textContent = aktif ? tampil + ' dari ' + cards.length + ' item ditampilkan' : '';
                    }
                }

                function terapkanGayaAktif(chip, aktif) {
                    chip.classList.toggle('bg-blue-600', aktif);
                    chip.classList.toggle('text-white', aktif);
                    chip.classList.toggle('border-blue-600', aktif);
                    chip.classList.toggle('bg-white', !aktif);
                    chip.classList.toggle('text-gray-600', !aktif);
                    chip.classList.toggle('border-gray-300', !aktif);
                    chip.setAttribute('aria-pressed', aktif ? 'true' : 'false');
                }

                search?.addEventListener('input', terapkan);

                for (const chip of chips) {
                    terapkanGayaAktif(chip, chip.dataset.value === 'semua');

                    chip.addEventListener('click', function () {
                        const grup = this.dataset.filter;
                        filters[grup] = this.dataset.value;

                        for (const c of chips.filter((x) => x.dataset.filter === grup)) {
                            terapkanGayaAktif(c, c === this);
                        }

                        terapkan();
                    });
                }
            });
        </script>
    @endpush
@endonce