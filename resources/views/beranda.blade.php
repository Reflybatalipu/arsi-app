@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

{{-- Popup ringkasan prioritas: hanya muncul saat baru login (flag session). --}}
@if (session('popup_peringatan'))
    <div id="popup-peringatan"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm transition-opacity duration-200"
         role="dialog" aria-modal="true" aria-labelledby="popup-judul">
        <div class="popup-panel card w-full max-w-md p-6 shadow-2xl">
            <div class="mb-4 flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-amber-100 text-lg text-amber-700">
                    <x-fa name="bell" />
                </span>
                <div class="min-w-0">
                    <h2 id="popup-judul" class="text-base font-semibold">Ringkasan Prioritas</h2>
                    <p class="text-sm text-gray-500">Selamat datang kembali, {{ auth()->user()->name }}.</p>
                </div>
            </div>

            @if ($notifikasi->isEmpty())
                <div class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <x-fa name="circle-check" class="text-emerald-600" />
                    Semua tugas dan agendamu masih aman. Kerja bagus!
                </div>
            @else
                <ul class="space-y-2 text-sm">
                    @foreach ($notifikasi->take(4) as $item)
                        <li class="flex items-center justify-between gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                            <span class="flex min-w-0 items-center gap-2">
                                <x-fa :name="$item->isTugas() ? 'list-check' : 'calendar-days'" class="text-gray-400" />
                                <span class="min-w-0 flex-1 truncate font-medium">{{ $item->judul() }}</span>
                            </span>
                            <span class="shrink-0 text-xs text-gray-500">{{ $item->sisaWaktu() }}</span>
                        </li>
                    @endforeach
                </ul>
                @if ($notifikasi->count() > 4)
                    <p class="mt-2 text-xs text-gray-400">+ {{ $notifikasi->count() - 4 }} peringatan lainnya.</p>
                @endif
            @endif

            <div class="mt-5 flex justify-end gap-2">
                <a href="#peringatan" data-tutup-popup class="btn btn-secondary btn-sm">
                    <x-fa name="bell" /> Lihat peringatan
                </a>
                <button type="button" data-tutup-popup class="btn btn-primary btn-sm">
                    <x-fa name="check" /> Mengerti
                </button>
            </div>
        </div>
    </div>
@endif

{{-- Header sapaan --}}
<div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Halo, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-gray-500">
            Kamu masuk sebagai <span class="font-medium text-gray-700">{{ auth()->user()->role->label() }}</span>.
            Atur waktumu antara tugas dan agenda hari ini.
        </p>
    </div>
    <a href="{{ route('tugas.index') }}" class="btn btn-primary">
        <x-fa name="list-check" /> Lihat Semua Tugas
    </a>
</div>

{{-- Kartu statistik --}}
<div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <x-stat-card label="Tugas Aktif" :value="$statistik['tugas_aktif']" tone="blue" icon="list-check" />
    <x-stat-card label="Tugas Selesai" :value="$statistik['tugas_selesai']" tone="green" icon="circle-check" />
    <x-stat-card label="Agenda Saya" :value="$statistik['agenda_total']" tone="indigo" icon="calendar-days" />
    <x-stat-card label="Peringatan" :value="$statistik['peringatan']" :tone="$statistik['peringatan'] > 0 ? 'red' : 'gray'" icon="bell" />
</div>

{{-- Panel notifikasi peringatan --}}
<section class="mb-8" id="peringatan">
    <h2 class="panel-judul mb-3">
        <x-fa name="bell" /> Peringatan
    </h2>

    @if ($notifikasi->isEmpty())
        <div class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <x-fa name="circle-check" class="text-emerald-600" />
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
<section class="mb-8 card p-4 sm:p-5">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <h2 class="panel-judul">
            <x-fa name="bullseye" /> Rekomendasi Pengerjaan
        </h2>
        <span class="text-xs text-gray-400"><x-fa name="arrow-down-wide-short" class="mr-1" />Diurutkan dari tenggat terdekat · tugas didahulukan</span>
    </div>

    @if ($rekomendasi->isEmpty())
        <p class="text-sm text-gray-500"><x-fa name="mug-hot" class="mr-1 text-gray-400" />Belum ada tugas atau agenda aktif. Saatnya beristirahat sebentar.</p>
    @else
        <ol class="space-y-2">
            @foreach ($rekomendasi as $index => $item)
                <x-reco-row :item="$item" :index="$index" />
            @endforeach
        </ol>
    @endif
</section>

{{-- Toolbar pencarian, filter & urutan --}}
<div class="card mb-8 p-3 sm:p-4">
    <div class="flex flex-col gap-3 xl:flex-row xl:items-center">
        <div class="relative flex-1">
            <x-fa name="magnifying-glass" class="pointer-events-none absolute inset-y-0 left-3 my-auto text-gray-400" />
            <input
                type="search"
                id="search"
                placeholder="Cari tugas atau agenda secara realtime..."
                autocomplete="off"
                class="input pl-9"
            >
        </div>

        <div class="flex flex-wrap items-center gap-2" id="filter-jenis" role="group" aria-label="Filter jenis">
            <x-fa name="filter" class="mr-1 text-gray-400" />
            @foreach (['semua' => 'Semua', 'tugas' => 'Tugas', 'agenda' => 'Agenda'] as $value => $label)
                <button type="button" data-filter="jenis" data-value="{{ $value }}"
                        class="filter-chip chip"
                        aria-pressed="{{ $value === 'semua' ? 'true' : 'false' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="flex flex-wrap items-center gap-2" id="filter-status" role="group" aria-label="Filter status">
            <x-fa name="circle-half-stroke" class="mr-1 text-gray-400" />
            @foreach (['semua' => 'Semua status', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'lewat' => 'Lewat waktu'] as $value => $label)
                <button type="button" data-filter="status" data-value="{{ $value }}"
                        class="filter-chip chip"
                        aria-pressed="{{ $value === 'semua' ? 'true' : 'false' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="flex flex-wrap items-center gap-2 xl:ml-auto">
            <label for="sort" class="text-xs text-gray-500"><x-fa name="arrow-down-wide-short" /></label>
            <select id="sort" class="input w-auto cursor-pointer py-1.5 text-xs">
                <option value="prioritas">Prioritas (urutan asli)</option>
                <option value="terdekat">Tenggat terdekat</option>
                <option value="terjauh">Tenggat terjauh</option>
                <option value="judul">Judul A–Z</option>
            </select>
            <button type="button" id="reset-filter" class="chip" title="Atur ulang filter dan urutan">
                <x-fa name="rotate-left" /> Atur ulang
            </button>
        </div>
    </div>

    <p class="mt-2 hidden text-xs text-gray-400" id="hasil-pencarian" aria-live="polite"></p>
</div>

{{-- Kartu tugas & agenda --}}
<div id="daftar-item" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($items as $item)
        <x-item-card :item="$item" :data-index="$loop->index" />
    @endforeach
</div>

<div id="kosong" class="card hidden border-dashed p-8 text-center text-sm text-gray-500">
    <x-fa name="inbox" class="mr-1 text-gray-400" />Tidak ada tugas atau agenda yang cocok dengan pencarian/filtermu.
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const search = document.getElementById('search');
                const sort = document.getElementById('sort');
                const reset = document.getElementById('reset-filter');
                const cards = Array.from(document.querySelectorAll('[data-item]'));
                const kosong = document.getElementById('kosong');
                const infoHasil = document.getElementById('hasil-pencarian');
                const chips = Array.from(document.querySelectorAll('.filter-chip'));

                if (!search && cards.length === 0) return;

                // ---- Filter & urutan yang dipersonalisasi (tersimpan per pengguna) ----
                const penyimpananKey = 'manajemen-tugas:filter:{{ auth()->id() }}';
                let tersimpan = {};
                try {
                    tersimpan = JSON.parse(localStorage.getItem(penyimpananKey) || '{}') || {};
                } catch (e) {
                    tersimpan = {};
                }

                const jenisValid = ['semua', 'tugas', 'agenda'];
                const statusValid = ['semua', 'aktif', 'selesai', 'lewat'];

                const filters = {
                    jenis: jenisValid.includes(tersimpan.jenis) ? tersimpan.jenis : 'semua',
                    status: statusValid.includes(tersimpan.status) ? tersimpan.status : 'semua',
                };

                search.value = typeof tersimpan.q === 'string' ? tersimpan.q : '';
                if (sort) sort.value = tersimpan.sort || 'prioritas';

                cards.forEach((card, i) => {
                    if (!card.dataset.index) card.dataset.index = i;
                });

                function simpanPreferensi() {
                    try {
                        localStorage.setItem(penyimpananKey, JSON.stringify({
                            q: search.value.trim(),
                            jenis: filters.jenis,
                            status: filters.status,
                            sort: sort ? sort.value : 'prioritas',
                        }));
                    } catch (e) { /* penyimpanan penuh/di-block — abaikan */ }
                }

                function urutkan() {
                    const mode = sort ? sort.value : 'prioritas';
                    const waktu = (card) => {
                        const v = card.dataset.deadline;
                        return v === '' ? null : Number(v);
                    };

                    const urut = [...cards];
                    if (mode === 'terdekat') {
                        urut.sort((a, b) => (waktu(a) ?? Infinity) - (waktu(b) ?? Infinity));
                    } else if (mode === 'terjauh') {
                        urut.sort((a, b) => (waktu(b) ?? -Infinity) - (waktu(a) ?? -Infinity));
                    } else if (mode === 'judul') {
                        urut.sort((a, b) => (a.dataset.title || '').localeCompare(b.dataset.title || '', 'id'));
                    } else {
                        urut.sort((a, b) => Number(a.dataset.index) - Number(b.dataset.index));
                    }

                    urut.forEach((card, i) => { card.style.order = i + 1; });
                }

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
                        const aktif = q || filters.jenis !== 'semua' || filters.status !== 'semua' || (sort && sort.value !== 'prioritas');
                        infoHasil.classList.toggle('hidden', !aktif);
                        infoHasil.textContent = aktif ? tampil + ' dari ' + cards.length + ' item ditampilkan' : '';
                    }
                }

                function terapkanGayaAktif(chip, aktif) {
                    chip.classList.toggle('chip-aktif', aktif);
                    chip.setAttribute('aria-pressed', aktif ? 'true' : 'false');
                }

                search?.addEventListener('input', function () {
                    terapkan();
                    simpanPreferensi();
                });

                sort?.addEventListener('change', function () {
                    urutkan();
                    terapkan();
                    simpanPreferensi();
                });

                reset?.addEventListener('click', function () {
                    filters.jenis = 'semua';
                    filters.status = 'semua';
                    search.value = '';
                    if (sort) sort.value = 'prioritas';

                    for (const c of chips) {
                        terapkanGayaAktif(c, c.dataset.value === 'semua');
                    }

                    try { localStorage.removeItem(penyimpananKey); } catch (e) { /* abaikan */ }

                    urutkan();
                    terapkan();
                });

                for (const chip of chips) {
                    terapkanGayaAktif(chip, filters[chip.dataset.filter] === chip.dataset.value);

                    chip.addEventListener('click', function () {
                        const grup = this.dataset.filter;
                        filters[grup] = this.dataset.value;

                        for (const c of chips.filter((x) => x.dataset.filter === grup)) {
                            terapkanGayaAktif(c, c === this);
                        }

                        terapkan();
                        simpanPreferensi();
                    });
                }

                urutkan();
                terapkan();

                // ---- Popup ringkasan prioritas ----
                const popup = document.getElementById('popup-peringatan');
                if (popup) {
                    const panel = popup.querySelector('.popup-panel');

                    const tutup = function () {
                        popup.classList.add('opacity-0', 'pointer-events-none');
                        setTimeout(function () { popup.remove(); }, 200);
                    };

                    // Klik di mana pun di luar panel popup → tutup popup.
                    popup.addEventListener('click', function (e) {
                        if (!panel.contains(e.target)) tutup();
                    });

                    for (const tombol of document.querySelectorAll('[data-tutup-popup]')) {
                        tombol.addEventListener('click', tutup);
                    }

                    // Tombol Escape juga menutup.
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') tutup();
                    });
                }
            });
        </script>
    @endpush
@endonce

@endsection
