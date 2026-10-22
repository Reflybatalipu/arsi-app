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
        <div class="card p-6 text-center text-sm text-gray-500">
            <x-fa name="inbox" class="mr-1 text-gray-400" />Belum ada tugas.
        </div>
    @else
        <div class="card overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50/70 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3"><x-fa name="font" class="mr-1 text-gray-400" />Judul</th>
                        <th class="px-4 py-3"><x-fa name="graduation-cap" class="mr-1 text-gray-400" />Mata Kuliah / Dosen</th>
                        <th class="px-4 py-3"><x-fa name="calendar-days" class="mr-1 text-gray-400" />Tenggat</th>
                        <th class="px-4 py-3"><x-fa name="users" class="mr-1 text-gray-400" />Selesai</th>
                        <th class="px-4 py-3"><x-fa name="circle-half-stroke" class="mr-1 text-gray-400" />Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($tugas as $item)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('tugas.show', $item) }}" class="font-medium text-accent hover:underline">
                                    <x-fa name="list-check" class="mr-1.5 text-gray-400" />{{ $item->judul }}
                                </a>
                                <p class="mt-0.5 text-xs text-gray-500"><x-fa name="user" class="mr-1" />Oleh {{ $item->user->name }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $item->mata_kuliah ?? '-' }}{{ $item->dosen ? ' · '.$item->dosen : '' }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($item->tenggat)
                                    <span class="{{ $item->isTenggatLewat() ? 'text-red-600' : 'text-gray-600' }}">
                                        <x-fa name="calendar-day" class="mr-1" />{{ $item->tenggat->isoFormat('D MMM Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <x-fa name="users" class="mr-1 text-gray-400" />{{ $item->diselesaikan_oleh_count }} mahasiswa
                            </td>
                            <td class="px-4 py-3">
                                @if (in_array($item->id, $tugasSelesaiIds))
                                    <span class="inline-flex items-center gap-1 rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                        <x-fa name="circle-check" />Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
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
