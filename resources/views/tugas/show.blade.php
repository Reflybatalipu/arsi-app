@extends('layouts.app')

@section('title', $tugas->judul)

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-semibold">{{ $tugas->judul }}</h1>

            <div class="flex gap-2">
                @can('update', $tugas)
                    <a href="{{ route('tugas.edit', $tugas) }}"
                       class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">Ubah</a>
                @endcan

                @can('delete', $tugas)
                    <form method="POST" action="{{ route('tugas.destroy', $tugas) }}"
                          onsubmit="return confirm('Hapus tugas ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-md border border-red-300 px-3 py-1.5 text-sm text-red-600 hover:bg-red-50">
                            Hapus
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            <dl class="space-y-3 text-sm">
                <div class="flex gap-3">
                    <dt class="w-32 shrink-0 text-gray-500">Mata Kuliah</dt>
                    <dd>{{ $tugas->mata_kuliah ?? '-' }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-32 shrink-0 text-gray-500">Dosen</dt>
                    <dd>{{ $tugas->dosen ?? '-' }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-32 shrink-0 text-gray-500">Tenggat</dt>
                    <dd class="{{ $tugas->isTenggatLewat() ? 'text-red-600' : '' }}">
                        {{ $tugas->tenggat?->isoFormat('D MMM Y') ?? 'Tanpa tenggat' }}
                    </dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-32 shrink-0 text-gray-500">Diinput oleh</dt>
                    <dd>{{ $tugas->user->name }}</dd>
                </div>
                @if ($tugas->deskripsi)
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-gray-500">Deskripsi</dt>
                        <dd class="whitespace-pre-line">{!! nl2br(e($tugas->deskripsi)) !!}</dd>
                    </div>
                @endif
            </dl>

            {{-- Tandai selesai --}}
            <div class="mt-6 border-t border-gray-100 pt-4">
                <form method="POST" action="{{ route('tugas.selesai', $tugas) }}">
                    @csrf
                    <button type="submit"
                            class="rounded-md px-4 py-2 text-sm font-medium {{ $sudahSelesai ? 'border border-gray-300 text-gray-600 hover:bg-gray-50' : 'bg-green-600 text-white hover:bg-green-700' }}">
                        {{ $sudahSelesai ? 'Batalkan Status Selesai' : 'Tandai Selesai' }}
                    </button>
                </form>
            </div>

            {{-- Progres mahasiswa --}}
            <div class="mt-6 border-t border-gray-100 pt-4">
                <h2 class="mb-2 text-sm font-semibold">
                    Diselesaikan {{ $tugas->diselesaikan_oleh_count }} mahasiswa
                </h2>

                @if ($penyelesai->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada yang menyelesaikan.</p>
                @else
                    <ul class="flex flex-wrap gap-2">
                        @foreach ($penyelesai as $mahasiswa)
                            <li class="rounded-full bg-green-50 px-2 py-0.5 text-xs text-green-700">
                                {{ $mahasiswa->name }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
