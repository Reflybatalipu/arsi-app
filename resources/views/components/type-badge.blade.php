@props(['type'])

@php
    $label = $type === \App\Support\ActivityItem::TYPE_TUGAS ? 'Tugas' : 'Agenda';
    $warna = $type === \App\Support\ActivityItem::TYPE_TUGAS
        ? 'bg-blue-100 text-blue-700'
        : 'bg-indigo-100 text-indigo-700';
@endphp

<span {{ $attributes->merge(['class' => "rounded px-2 py-0.5 text-[11px] font-medium {$warna}"]) }}>
    {{ $label }}
</span>