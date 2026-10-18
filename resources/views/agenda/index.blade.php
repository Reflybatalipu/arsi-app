@extends('layouts.app')

@section('title', 'Agenda Saya')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">Agenda Saya</h1>

        <a href="{{ route('agenda.create') }}"
           class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            + Tambah Agenda
        </a>
    </div>

    @if ($agendas->isEmpty())
        <div class="rounded-lg bg-white p-6 text-center text-sm text-gray-500 shadow">
            Belum ada agenda. Klik "Tambah Agenda" untuk membuat.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($agendas as $agenda)
                <a href="{{ route('agenda.show', $agenda) }}"
                   class="flex items-center justify-between gap-4 rounded-lg bg-white p-4 shadow hover:bg-gray-50">
                    <div>
                        <p class="font-medium">{{ $agenda->judul }}</p>
                        <p class="text-xs text-gray-500">{{ $agenda->deskripsi }}</p>
                    </div>
                    <div class="shrink-0 text-right text-sm">
                        @if ($agenda->waktuMulai()->isPast())
                            <span class="mb-1 inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                                Selesai
                            </span>
                        @else
                            <span class="mb-1 inline-block rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">
                                Akan datang
                            </span>
                        @endif
                        <p>{{ $agenda->tanggal->isoFormat('D MMM Y') }}{{ $agenda->waktu ? ' · '.$agenda->waktu : '' }}</p>
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">{{ $agenda->jenis }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $agendas->links() }}
        </div>
    @endif
@endsection
