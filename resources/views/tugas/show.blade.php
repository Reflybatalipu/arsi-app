@extends('layouts.app')

@section('title', $tugas->judul)

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h1 class="flex items-center gap-2 text-xl font-semibold">
                <x-fa name="list-check" class="text-accent" />{{ $tugas->judul }}
            </h1>

            <div class="flex gap-2">
                @can('update', $tugas)
                    <a href="{{ route('tugas.edit', $tugas) }}" class="btn btn-secondary btn-sm">
                        <x-fa name="pen" /> Ubah
                    </a>
                @endcan

                @can('delete', $tugas)
                    <form method="POST" action="{{ route('tugas.destroy', $tugas) }}"
                          onsubmit="return confirm('Hapus tugas ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <x-fa name="trash" /> Hapus
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="card p-6">
            <dl class="space-y-3 text-sm">
                <div class="flex gap-3">
                    <dt class="dt-label"><x-fa name="graduation-cap" class="text-ink-4" />Mata Kuliah</dt>
                    <dd>{{ $tugas->mata_kuliah ?? '-' }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="dt-label"><x-fa name="user-tie" class="text-ink-4" />Dosen</dt>
                    <dd>{{ $tugas->dosen ?? '-' }}</dd>
                </div>
                <div class="flex gap-3">
                    <dt class="dt-label"><x-fa name="calendar-days" class="text-ink-4" />Tenggat</dt>
                    <dd class="{{ $tugas->isTenggatLewat() ? 'text-red-600 dark:text-red-400' : '' }}">
                        {{ $tugas->tenggat?->isoFormat('D MMM Y') ?? 'Tanpa tenggat' }}
                    </dd>
                </div>
                <div class="flex gap-3">
                    <dt class="dt-label"><x-fa name="user-pen" class="text-ink-4" />Diinput oleh</dt>
                    <dd>{{ $tugas->user->name }}</dd>
                </div>
                @if ($tugas->deskripsi)
                    <div class="flex gap-3">
                        <dt class="dt-label"><x-fa name="align-left" class="text-ink-4" />Deskripsi</dt>
                        <dd class="whitespace-pre-line">{!! nl2br(e($tugas->deskripsi)) !!}</dd>
                    </div>
                @endif
            </dl>

            {{-- Tandai selesai --}}
            <div class="mt-6 border-t border-line pt-4">
                <form method="POST" action="{{ route('tugas.selesai', $tugas) }}">
                    @csrf
                    <button type="submit" class="btn {{ $sudahSelesai ? 'btn-secondary' : 'btn-success' }}">
                        <x-fa name="{{ $sudahSelesai ? 'rotate-left' : 'circle-check' }}" />
                        {{ $sudahSelesai ? 'Batalkan Status Selesai' : 'Tandai Selesai' }}
                    </button>
                </form>
            </div>

            {{-- Progres mahasiswa --}}
            <div class="mt-6 border-t border-line pt-4">
                <h2 class="mb-2 flex items-center gap-2 text-sm font-semibold">
                    <x-fa name="users" class="text-ink-4" />Diselesaikan {{ $tugas->diselesaikan_oleh_count }} mahasiswa
                </h2>

                @if ($penyelesai->isEmpty())
                    <p class="text-sm text-ink-3"><x-fa name="inbox" class="mr-1 text-ink-4" />Belum ada yang menyelesaikan.</p>
                @else
                    <ul class="flex flex-wrap gap-2">
                        @foreach ($penyelesai as $mahasiswa)
                            <li class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                <x-fa name="user-graduate" />{{ $mahasiswa->name }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
