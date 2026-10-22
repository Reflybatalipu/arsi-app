@extends('layouts.app')

@section('title', 'Daftar Tugas')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="flex items-center gap-2 text-xl font-semibold"><x-fa name="list-check" class="text-accent" />Daftar Tugas</h1>

        @can('create', App\Models\Tugas::class)
            <a href="{{ route('tugas.create') }}" class="btn btn-primary">
                <x-fa name="plus" /> Tambah Tugas
            </a>
        @endcan
    </div>

    {{-- Filter tenggat --}}
    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('tugas.index') }}"
           class="chip {{ request('tenggat') === null ? 'chip-aktif' : '' }}">
            <x-fa name="layer-group" /> Semua
        </a>
        <a href="{{ route('tugas.index', ['tenggat' => 'akan']) }}"
           class="chip {{ request('tenggat') === 'akan' ? 'chip-aktif' : '' }}">
            <x-fa name="calendar-day" /> Akan Datang
        </a>
        <a href="{{ route('tugas.index', ['tenggat' => 'lewat']) }}"
           class="chip {{ request('tenggat') === 'lewat' ? 'chip-aktif' : '' }}">
            <x-fa name="triangle-exclamation" /> Lewat Waktu
        </a>
    </div>

    @if ($tugas->isEmpty())
        <div class="card p-6 text-center text-sm text-ink-3">
            <x-fa name="inbox" class="mr-1 text-ink-4" />Belum ada tugas.
        </div>
    @else
        <div class="card overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-line bg-surface-2/70 text-xs uppercase text-ink-3">
                    <tr>
                        <th class="px-4 py-3"><x-fa name="font" class="mr-1 text-ink-4" />Judul</th>
                        <th class="px-4 py-3"><x-fa name="graduation-cap" class="mr-1 text-ink-4" />Mata Kuliah / Dosen</th>
                        <th class="px-4 py-3"><x-fa name="calendar-days" class="mr-1 text-ink-4" />Tenggat</th>
                        <th class="px-4 py-3"><x-fa name="users" class="mr-1 text-ink-4" />Selesai</th>
                        <th class="px-4 py-3"><x-fa name="circle-half-stroke" class="mr-1 text-ink-4" />Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($tugas as $item)
                        <tr class="transition hover:bg-surface-2">
                            <td class="px-4 py-3">
                                <a href="{{ route('tugas.show', $item) }}" class="font-medium text-accent hover:underline">
                                    <x-fa name="list-check" class="mr-1.5 text-ink-4" />{{ $item->judul }}
                                </a>
                                <p class="mt-0.5 text-xs text-ink-3"><x-fa name="user" class="mr-1" />Oleh {{ $item->user->name }}</p>
                            </td>
                            <td class="px-4 py-3 text-ink-2">
                                {{ $item->mata_kuliah ?? '-' }}{{ $item->dosen ? ' · '.$item->dosen : '' }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($item->tenggat)
                                    <span class="{{ $item->isTenggatLewat() ? 'text-red-600 dark:text-red-400' : 'text-ink-2' }}">
                                        <x-fa name="calendar-day" class="mr-1" />{{ $item->tenggat->isoFormat('D MMM Y') }}
                                    </span>
                                @else
                                    <span class="text-ink-4">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-ink-2">
                                <x-fa name="users" class="mr-1 text-ink-4" />{{ $item->diselesaikan_oleh_count }} mahasiswa
                            </td>
                            <td class="px-4 py-3">
                                @if (in_array($item->id, $tugasSelesaiIds))
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300">
                                        <x-fa name="circle-check" />Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-md bg-black/5 px-2 py-0.5 text-xs font-medium text-ink-2 dark:bg-white/10">
                                        <x-fa name="hourglass-half" />Belum
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tugas->links() }}
        </div>
    @endif
@endsection
