@extends('layouts.app')

@section('title', 'Agenda Saya')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="flex items-center gap-2 text-xl font-semibold"><x-fa name="calendar-days" class="text-indigo-500" />Agenda Saya</h1>

        <a href="{{ route('agenda.create') }}" class="btn btn-primary">
            <x-fa name="plus" /> Tambah Agenda
        </a>
    </div>

    @if ($agendas->isEmpty())
        <div class="card p-6 text-center text-sm text-gray-500">
            <x-fa name="inbox" class="mr-1 text-gray-400" />Belum ada agenda. Klik "Tambah Agenda" untuk membuat.
        </div>
    @else
        <div class="space-y-3">
            @foreach ($agendas as $agenda)
                <a href="{{ route('agenda.show', $agenda) }}"
                   class="card flex items-center justify-between gap-4 p-4 transition hover:bg-gray-50">
                    <div class="min-w-0">
                        <p class="flex items-center gap-2 font-medium">
                            <x-fa name="calendar-day" class="text-indigo-400" />{{ $agenda->judul }}
                        </p>
                        <p class="mt-0.5 text-xs text-gray-500">{{ $agenda->deskripsi }}</p>
                    </div>
                    <div class="shrink-0 text-right text-sm">
                        @if ($agenda->waktuMulai()->isPast())
                            <span class="mb-1 inline-flex items-center gap-1 rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                <x-fa name="circle-check" />Selesai
                            </span>
                        @else
                            <span class="mb-1 inline-flex items-center gap-1 rounded-md bg-accent-soft px-2 py-0.5 text-xs font-medium text-accent">
                                <x-fa name="clock" />Akan datang
                            </span>
                        @endif
                        <p class="mt-1 text-xs text-gray-600">
                            <x-fa name="calendar-days" class="mr-1" />{{ $agenda->tanggal->isoFormat('D MMM Y') }}{{ $agenda->waktu ? ' · '.$agenda->waktu : '' }}
                        </p>
                        <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-0.5 text-xs text-gray-600">
                            <x-fa name="tag" />{{ $agenda->jenis }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $agendas->links() }}
        </div>
    @endif
@endsection
