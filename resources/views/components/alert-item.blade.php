@props(['item'])

@php
    $sisa = $item->sisaDetik();
    $lewatTugas = $item->isTugas() && $sisa !== null && $sisa < 0;
    $agendaSelesai = ! $item->isTugas() && $item->selesaiOtomatis();

    // Tugas lewat (merah), agenda selesai otomatis (hijau), sisanya perhatian (kuning).
    [$tema, $ikon] = match (true) {
        $lewatTugas => ['border-red-200 bg-red-50 text-red-800', 'triangle-exclamation'],
        $agendaSelesai => ['border-emerald-200 bg-emerald-50 text-emerald-800', 'circle-check'],
        $item->isTugas() => ['border-amber-200 bg-amber-50 text-amber-800', 'clock'],
        default => ['border-amber-200 bg-amber-50 text-amber-800', 'calendar-day'],
    };

    $judulNotifikasi = match (true) {
        $lewatTugas => 'Tenggat terlampaui: '.$item->judul(),
        $agendaSelesai => 'Agenda selesai: '.$item->judul(),
        $item->isTugas() => 'Tugas mendekati tenggat: '.$item->judul(),
        default => 'Agenda segera dimulai: '.$item->judul(),
    };

    $tautan = $item->isTugas()
        ? route('tugas.show', $item->model)
        : route('agenda.show', $item->model);

    $labelTautan = $item->isTugas() ? 'Buka Tugas' : 'Buka Agenda';

    $batasTautan = $lewatTugas
        ? 'border-red-300 text-red-700 hover:bg-red-100'
        : ($agendaSelesai
            ? 'border-emerald-300 text-emerald-700 hover:bg-emerald-100'
            : 'border-amber-300 text-amber-700 hover:bg-amber-100');
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-lg border px-4 py-3 shadow-sm {$tema}"]) }}>
    <x-fa :name="$ikon" class="mt-1" />

    <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold">{{ $judulNotifikasi }}</p>
        <p class="mt-0.5 text-xs opacity-80">
            @if ($agendaSelesai)
                Otomatis ditandai selesai setelah waktu pelaksanaan.
            @elseif ($item->isTugas() && ! $item->selesai)
                Kerjakan sekarang sebelum tenggat lewat.
            @elseif ($item->perluPengingatAgenda())
                Persiapkan dirimu.
            @endif
            Waktu: {{ $item->waktuLabel() }} — {{ $item->sisaWaktu() }}
        </p>
    </div>

    <a href="{{ $tautan }}"
       class="inline-flex shrink-0 items-center gap-1.5 rounded-md border bg-white/70 px-3 py-1.5 text-xs font-medium {{ $batasTautan }}">
        <x-fa name="arrow-up-right-from-square" />{{ $labelTautan }}
    </a>
</div>
