@props(['label', 'value', 'tone' => 'blue', 'icon' => 'chart-simple'])

@php
    $warna = match ($tone) {
        'blue' => 'bg-accent-soft text-accent',
        'green' => 'bg-emerald-50 text-emerald-700',
        'indigo' => 'bg-indigo-500/10 text-indigo-600',
        'red' => 'bg-red-500/10 text-red-600',
        default => 'bg-gray-400/10 text-gray-500',
    };
@endphp

<div {{ $attributes->merge(['class' => 'card flex items-center gap-4 p-4']) }}>
    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md text-lg {{ $warna }}">
        <x-fa :name="$icon" />
    </span>

    <div class="min-w-0">
        <p class="truncate text-xs font-medium text-gray-500">{{ $label }}</p>
        <p class="text-2xl font-semibold leading-tight">{{ $value }}</p>
    </div>
</div>
