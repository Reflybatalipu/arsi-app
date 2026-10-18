@props(['priority'])

@php
    $warna = match ($priority->value) {
        'kritis' => 'bg-red-100 text-red-700',
        'tinggi' => 'bg-orange-100 text-orange-700',
        'sedang' => 'bg-yellow-100 text-yellow-700',
        default => 'bg-green-100 text-green-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "rounded px-2 py-0.5 text-[11px] font-semibold {$warna}"]) }}>
    Prioritas {{ $priority->label() }}
</span>