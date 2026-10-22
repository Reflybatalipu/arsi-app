@props(['type'])

@php
    $tugas = $type === \App\Support\ActivityItem::TYPE_TUGAS;
    $label = $tugas ? 'Tugas' : 'Agenda';
    $warna = $tugas
        ? 'bg-accent-soft text-accent'
        : 'bg-indigo-500/10 text-indigo-600';
    $ikon = $tugas ? 'list-check' : 'calendar-days';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[11px] font-medium {$warna}"]) }}>
    <x-fa :name="$ikon" />{{ $label }}
</span>
