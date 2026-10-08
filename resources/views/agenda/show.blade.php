@extends('layouts.app')

@section('title', $agenda->judul)

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-semibold">{{ $agenda->judul }}</h1>

            <div class="flex gap-2">
                <a href="{{ route('agenda.edit', $agenda) }}"
                   class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">Ubah</a>

                <form method="POST" action="{{ route('agenda.destroy', $agenda) }}"
                      onsubmit="return confirm('Hapus agenda ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-md border border-red-300 px-3 py-1.5 text-sm text-red-600 hover:bg-red-50">
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            <dl class="space-y-3 text-sm">
                <div class="flex gap-3">
                    <dt class="w-32 shrink-0 text-gray-500">Jenis</dt>
                    <dd>
                        <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs text-blue-700">{{ $agenda->jenis }}</span>
                    </dd>
                </div>
                <div class="flex gap-3">
                    <dt class="w-32 shrink-0 text-gray-500">Tanggal</dt>
                    <dd>{{ $agenda->tanggal->isoFormat('D MMM Y') }}</dd>
                </div>
                @if ($agenda->waktu)
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-gray-500">Waktu</dt>
                        <dd>{{ $agenda->waktu }}</dd>
                    </div>
                @endif
                @if ($agenda->deskripsi)
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-gray-500">Deskripsi</dt>
                        <dd class="whitespace-pre-line">{!! nl2br(e($agenda->deskripsi)) !!}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>
@endsection
