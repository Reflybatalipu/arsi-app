@props(['priority'])

@php
    $warna = match ($priority->value) {
        'kritis' => 'bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-300',
        'tinggi' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/60 dark:text-orange-300',
        'sedang' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300',
        default => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300',
    };

    $ikon = match ($priority->value) {
        'kritis' => 'circle-exclamation',
        'tinggi' => 'bolt',
        'sedang' => 'gauge-high',
        default => 'leaf',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[11px] font-semibold {$warna}"]) }}>
    <x-fa :name="$ikon" />Prioritas {{ $priority->label() }}
</span>
