@props(['label', 'value', 'tone' => 'blue'])

@php
    $warna = match ($tone) {
        'blue' => 'text-blue-600',
        'green' => 'text-green-600',
        'indigo' => 'text-indigo-600',
        'red' => 'text-red-600',
        default => 'text-gray-400',
    };
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 bg-white p-4 shadow-sm']) }}>
    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $label }}</p>
    <p class="mt-1 text-3xl font-bold {{ $warna }}">{{ $value }}</p>
</div>