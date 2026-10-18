@extends('layouts.app')

@section('title', 'Beranda')

@php
    $jenisLabel = ['organisasi' => 'Organisasi', 'rencana' => 'Rencana', 'lainnya' => 'Lainnya'];

    $statusItem = function ($item) {
        if ($item->isTugas() && $item->selesai) {
            return 'selesai';
        }

        if ($item->sisaDetik() !== null && $item->sisaDetik() < 0) {
            return 'lewat';
        }

        return 'aktif';
    };
@endphp

{{-- Header sapaan --}}
<div class="mb-6 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Halo, {{ auth()->user()->name }} 👋</h1>
        <p class="text-sm text-gray-500">
            Kamu masuk sebagai <span class="font-medium text-gray-700">{{ auth()->user()->role->label() }}</span>.
            Atur waktumu antara tugas dan agenda hari ini.
        </p>
    </div>
    <a href="{{ route('tugas.index') }}"
       class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
        Lihat Semua Tugas
    </a>
</div>

{{-- Kartu statistik --}}
<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Tugas Aktif</p>
        <p class="mt-1 text-3xl font-bold text-blue-600">{{ $statistik['tugas_aktif'] }}</p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Tugas Selesai</p>
        <p class="mt-1 text-3xl font-bold text-green-600">{{ $statistik['tugas_selesai'] }}</p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Agenda Saya</p>
        <p class="mt-1 text-3xl font-bold text-indigo-600">{{ $statistik['agenda_total'] }}</p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Peringatan</p>
        <p class="mt-1 text-3xl font-bold {{ $statistik['peringatan'] > 0 ? 'text-red-600' : 'text-gray-400' }}">
            {{ $statistik['peringatan'] }}
        </p>
    </div>
</div>

{{-- Panel notifikasi peringatan --}}
<section id="panel-notifikasi" class="mb-6">
    <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-500">
        <span aria-hidden="true">🔔</span> Peringatan Mendesak
    </h2>

    @if ($notifikasi->isEmpty())
        <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <span aria-hidden="true">✅</span>
            Tidak ada peringatan. Semua tugas dan agendamu masih aman.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($notifikasi as $item)
                @php
                    $sisa = $item->sisaDetik();
                    $telat = $sisa !== null && $sisa < 0 && $item->isTugas();
                @endphp
                <div class="flex items-start gap-3 rounded-xl border px-4 py-3 shadow-sm
                    {{ $telat || $item->isTugas()
                        ? 'border-red-200 bg-red-50 text-red-800'
                        : 'border-amber-200 bg-amber-50 text-amber-800' }}">
                    <span class="mt-0.5 text-lg" aria-hidden="true">{{ $telat ? '🚨' : ($item->isTugas() ? '⏰' : '📅') }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold">
                            @if ($item->isTugas())
                                {{ $telat ? 'Tenggat terlampaui' : 'Tugas mendekati tenggat' }}: {{ $item->judul() }}
                            @else
                                Agenda segera dimulai: {{ $item->judul() }}
                            @endif
                        </p>
                        <p class="mt-0.5 text-xs opacity-80">
                            {{ $item->isTugas()
                                ? ($item->selesai ? '' : 'Kerjakan sekarang sebelum tenggat lewat. ')
                                : 'Persiapkan dirimu. ' }}
                            Waktu: {{ $item->deadline?->isoFormat('D MMM Y, HH:mm') ?? '-' }} — {{ $item->sisaWaktu() }}
                        </p>
                    </div>
                    @if ($item->isTugas())
                        <a href="{{ route('tugas.show', $item->model) }}"
                           class="shrink-0 rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100">
                            Buka Tugas
                        </a>
                    @else
                        <a href="{{ route('agenda.show', $item->model) }}"
                           class="shrink-0 rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-xs font-medium text-amber-700 hover:bg-amber-100">
                            Buka Agenda
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- Rekomendasi pengerjaan --}}
<section class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
    <div class="mb-3 flex items-center justify-between">
        <h2 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-500">
            <span aria-hidden="true">🎯</span> Rekomendasi Pengerjaan
        </h2>
        <span class="text-xs text-gray-400">Diurutkan dari tenggat terdekat · tugas didahulukan</span>
    </div>

    @if ($rekomendasi->isEmpty())
        <p class="text-sm text-gray-500">Belum ada tugas atau agenda. Saatnya bersantang sebentar ☕</p>
    @else
        <ol class="space-y-2">
            @foreach ($rekomendasi as $index => $item)
                <li class="flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2.5">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold
                        {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 border border-gray-200' }}">
                        {{ $index + 1 }}
                    </span>

                    <span class="shrink-0 rounded px-2 py-0.5 text-[11px] font-medium
                        {{ $item->isTugas() ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }}">
                        {{ $item->isTugas() ? 'Tugas' : 'Agenda' }}
                    </span>

                    <a href="{{ $item->isTugas() ? route('tugas.show', $item->model) : route('agenda.show', $item->model) }}"
                       class="min-w-0 flex-1 truncate text-sm font-medium text-gray-800 hover:text-blue-600">
                        {{ $item->judul() }}
                    </a>

                    @if ($item->priority() !== null)
                        <span class="shrink-0 rounded px-2 py-0.5 text-[11px] font-semibold
                            @switch($item->priority()->value)
                                @case('kritis') bg-red-100 text-red-700 @break
                                @case('tinggi') bg-orange-100 text-orange-700 @break
                                @case('sedang') bg-yellow-100 text-yellow-700 @break
                                @default bg-green-100 text-green-700
                            @endswitch">
                            Prioritas {{ $item->priority()->label() }}
                        </span>
                    @endif

                    <span class="shrink-0 text-xs font-medium {{ ($item->sisaDetik() ?? 1) < 0 ? 'text-red-600' : ($statusItem($item) === 'selesai' ? 'text-green-600' : 'text-gray-500') }}">
                        {{ $item->sisaWaktu() }}
                    </span>
                </li>
            @endforeach
        </ol>
    @endif
</section>

{{-- Toolbar pencarian & filter --}}
<div class="sticky top-0 z-10 -mx-1 mb-6 rounded-xl border border-gray-200 bg-white/95 p-3 shadow-sm backdrop-blur sm:p-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400" aria-hidden="true">🔍</span>
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
        @php
            $status = $statusItem($item);
            $searchText = strtolower(trim(
                $item->judul().' '.
                ($item->model->getAttribute('mata_kuliah') ?? '').' '.
                ($item->model->getAttribute('dosen') ?? '').' '.
                ($jenisLabel[$item->model->getAttribute('jenis')] ?? '').' '.
                ($item->model->getAttribute('deskripsi') ?? '')
            ));
        @endphp
        <article
            data-item
            data-type="{{ $item->type }}"
            data-status="{{ $status }}"
            data-search="{{ $searchText }}"
            class="card-item group relative flex flex-col rounded-xl border bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md
                @if ($status === 'lewat') border-red-300 @elseif(($item->sisaDetik() ?? PHP_INT_MAX) <= 24 * 3600 && $status !== 'selesai') border-amber-300 @else border-gray-200 @endif"
        >
            <div class="mb-2 flex items-start justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="rounded px-2 py-0.5 text-[11px] font-medium
                        {{ $item->isTugas() ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }}">
                        {{ $item->isTugas() ? 'Tugas' : 'Agenda' }}
                    </span>

                    @if ($status === 'selesai')
                        <span class="rounded bg-green-100 px-2 py-0.5 text-[11px] font-medium text-green-700">Selesai</span>
                    @elseif ($status === 'lewat')
                        <span class="rounded bg-red-100 px-2 py-0.5 text-[11px] font-medium text-red-700">Lewat waktu</span>
                    @endif
                </div>

                @if ($item->priority() !== null)
                    <span class="shrink-0 rounded px-2 py-0.5 text-[11px] font-semibold
                        @switch($item->priority()->value)
                            @case('kritis') bg-red-100 text-red-700 @break
                            @case('tinggi') bg-orange-100 text-orange-700 @break
                            @case('sedang') bg-yellow-100 text-yellow-700 @break
                            @default bg-green-100 text-green-700
                        @endswitch">
                        {{ $item->priority()->label() }}
                    </span>
                @endif
            </div>

            <a href="{{ $item->isTugas() ? route('tugas.show', $item->model) : route('agenda.show', $item->model) }}"
               class="text-sm font-semibold text-gray-900 hover:text-blue-600">
                {{ $item->judul() }}
            </a>

            <p class="mt-1 flex-1 text-xs text-gray-500 line-clamp-3">
                @if ($item->isTugas())
                    {{ $item->model->mata_kuliah ? $item->model->mata_kuliah : 'Tanpa mata kuliah' }}
                    {{ $item->model->dosen ? '· '.$item->model->dosen : '' }}
                @else
                    {{ $jenisLabel[$item->model->jenis] ?? 'Agenda' }}
                    @if ($item->model->deskripsi) · {{ \Illuminate\Support\Str::limit($item->model->deskripsi, 60) }} @endif
                @endif
            </p>

            <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 text-xs">
                @if ($item->isTugas())
                    <span class="text-gray-500">
                        📅 {{ $item->deadline?->isoFormat('D MMM Y') ?? 'Tanpa tenggat' }}
                    </span>
                @else
                    <span class="text-gray-500">
                        📅 {{ $item->deadline?->isoFormat('D MMM Y') }}
                        {{ $item->model->waktu ? '· '.$item->model->waktu : '' }}
                    </span>
                @endif

                <span class="font-medium {{ ($item->sisaDetik() ?? 1) < 0 ? 'text-red-600' : ($status === 'selesai' ? 'text-green-600' : 'text-blue-600') }}">
                    {{ $item->sisaWaktu() }}
                </span>
            </div>
        </article>
    @endforeach
</div>

<div id="kosong" class="hidden rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
    Tidak ada tugas atau agenda yang cocok dengan pencarian/filtermu.
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const search = document.getElementById('search');
                const cards = Array.from(document.querySelectorAll('[data-item]'));
                const kosong = document.getElementById('kosong');
                const infoHasil = document.getElementById('hasil-pencarian');
                const chips = Array.from(document.querySelectorAll('.filter-chip'));

                if (!search || cards.length === 0 && !kosong) return;

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

                search?.addEventListener('input', terapkan);

                for (const chip of chips) {
                    chip.addEventListener('click', function () {
                        const grup = this.dataset.filter;
                        filters[grup] = this.dataset.value;

                        for (const c of chips.filter((x) => x.dataset.filter === grup)) {
                            const aktif = c === this;
                            c.classList.toggle('bg-blue-600', aktif);
                            c.classList.toggle('text-white', aktif);
                            c.classList.toggle('border-blue-600', aktif);
                            c.classList.toggle('bg-white', !aktif);
                            c.classList.toggle('text-gray-600', !aktif);
                            c.classList.toggle('border-gray-300', !aktif);
                            c.setAttribute('aria-pressed', aktif ? 'true' : 'false');
                        }

                        terapkan();
                    });
                }

                // Status awal chip: "semua" aktif.
                for (const c of chips) {
                    const aktif = c.dataset.value === 'semua';
                    c.classList.toggle('bg-blue-600', aktif);
                    c.classList.toggle('text-white', aktif);
                    c.classList.toggle('border-blue-600', aktif);
                    c.classList.toggle('bg-white', !aktif);
                    c.classList.toggle('text-gray-600', !aktif);
                    c.classList.toggle('border-gray-300', !aktif);
                }
            });
        </script>
    @endpush
@endonce