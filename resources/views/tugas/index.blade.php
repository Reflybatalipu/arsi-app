@extends('layouts.app')

@section('title', 'Daftar Tugas')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">Daftar Tugas</h1>

        @can('create', App\Models\Tugas::class)
            <a href="{{ route('tugas.create') }}"
               class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                + Tambah Tugas
            </a>
        @endcan
    </div>

    {{-- Filter tenggat --}}
    <div class="mb-4 flex gap-2 text-sm">
        <a href="{{ route('tugas.index') }}"
           class="rounded-full px-3 py-1 {{ request('tenggat') === null ? 'bg-blue-600 text-white' : 'bg-white text-gray-600' }}">
            Semua
        </a>
        <a href="{{ route('tugas.index', ['tenggat' => 'akan']) }}"
           class="rounded-full px-3 py-1 {{ request('tenggat') === 'akan' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600' }}">
            Akan Datang
        </a>
        <a href="{{ route('tugas.index', ['tenggat' => 'lewat']) }}"
           class="rounded-full px-3 py-1 {{ request('tenggat') === 'lewat' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600' }}">
            Lewat Waktu
        </a>
    </div>

    @if ($tugas->isEmpty())
        <div class="rounded-lg bg-white p-6 text-center text-sm text-gray-500 shadow">
            Belum ada tugas.
        </div>
    @else
        <div class="overflow-x-auto rounded-lg bg-white shadow">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Mata Kuliah / Dosen</th>
                        <th class="px-4 py-3">Tenggat</th>
                        <th class="px-4 py-3">Selesai</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($tugas as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('tugas.show', $item) }}" class="font-medium text-blue-600 hover:underline">
                                    {{ $item->judul }}
                                </a>
                                <p class="text-xs text-gray-500">Oleh {{ $item->user->name }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $item->mata_kuliah ?? '-' }}{{ $item->dosen ? ' · '.$item->dosen : '' }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($item->tenggat)
                                    <span class="{{ $item->isTenggatLewat() ? 'text-red-600' : 'text-gray-600' }}">
                                        {{ $item->tenggat->isoFormat('D MMM Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $item->diselesaikan_oleh_count }} mahasiswa
                            </td>
                            <td class="px-4 py-3">
                                @if (in_array($item->id, $tugasSelesaiIds))
                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Selesai</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Belum</span>
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
