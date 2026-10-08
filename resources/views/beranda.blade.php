@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <h1 class="mb-1 text-xl font-semibold">Halo, {{ auth()->user()->name }}</h1>
    <p class="mb-6 text-sm text-gray-600">
        Kamu masuk sebagai <span class="font-medium">{{ auth()->user()->role->label() }}</span>.
    </p>

    {{-- Statistik --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-lg bg-white p-4 shadow">
            <p class="text-sm text-gray-500">Total Tugas</p>
            <p class="text-2xl font-semibold">{{ $statistik['tugas_total'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow">
            <p class="text-sm text-gray-500">Tugas Selesai</p>
            <p class="text-2xl font-semibold text-green-600">{{ $statistik['tugas_selesai'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow">
            <p class="text-sm text-gray-500">Agenda Saya</p>
            <p class="text-2xl font-semibold text-blue-600">{{ $statistik['agenda_total'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Tugas terdekat --}}
        <section class="rounded-lg bg-white p-4 shadow">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-semibold">Tugas Terdekat</h2>
                <a href="{{ route('tugas.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
            </div>

            @if ($tugasTerdekat->isEmpty())
                <p class="text-sm text-gray-500">Belum ada tugas.</p>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach ($tugasTerdekat as $tugas)
                        <li class="flex items-center justify-between gap-3 py-2">
                            <a href="{{ route('tugas.show', $tugas) }}" class="text-sm hover:text-blue-600">
                                {{ $tugas->judul }}
                            </a>
                            <span class="shrink-0 text-xs text-gray-500">
                                {{ $tugas->tenggat?->isoFormat('D MMM Y') ?? 'Tanpa tenggat' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Agenda terdekat --}}
        <section class="rounded-lg bg-white p-4 shadow">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="font-semibold">Agenda Saya</h2>
                <a href="{{ route('agenda.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
            </div>

            @if ($agendaTerdekat->isEmpty())
                <p class="text-sm text-gray-500">Belum ada agenda.</p>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach ($agendaTerdekat as $agenda)
                        <li class="flex items-center justify-between gap-3 py-2">
                            <a href="{{ route('agenda.show', $agenda) }}" class="text-sm hover:text-blue-600">
                                {{ $agenda->judul }}
                            </a>
                            <span class="shrink-0 text-xs text-gray-500">
                                {{ $agenda->tanggal->isoFormat('D MMM Y') }}{{ $agenda->waktu ? ', '.$agenda->waktu : '' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
