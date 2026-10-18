@props(['item'])

@php
    $status = $item->status();
    $mendekati = ($item->sisaDetik() ?? PHP_INT_MAX) <= \App\Support\ActivityItem::JENDELA_TUGAS_JAM * 3600
        && $status !== \App\Support\ActivityItem::STATUS_SELESAI;

    $batasWarna = match (true) {
        $status === \App\Support\ActivityItem::STATUS_LEWAT => 'border-red-300',
        $mendekati => 'border-amber-300',
        default => 'border-gray-200',
    };

    $waktuWarna = match (true) {
        ($item->sisaDetik() ?? 1) < 0 => 'text-red-600',
        $status === \App\Support\ActivityItem::STATUS_SELESAI => 'text-green-600',
        default => 'text-blue-600',
    };
@endphp

<article
    data-item
    data-type="{{ $item->type }}"
    data-status="{{ $status }}"
    data-search="{{ $item->searchText() }}"
    {{ $attributes->merge(['class' => "card-item flex flex-col rounded-xl border bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {$batasWarna}"]) }}
>
    <div class="mb-2 flex items-start justify-between gap-2">
        <div class="flex items-center gap-2">
            <x-type-badge :type="$item->type" />
            <x-status-badge :status="$status" />
        </div>

        @if ($item->priority() !== null)
            <x-priority-badge :priority="$item->priority()" class="shrink-0" />
        @endif
    </div>

    <a href="{{ $item->isTugas() ? route('tugas.show', $item->model) : route('agenda.show', $item->model) }}"
       class="text-sm font-semibold text-gray-900 hover:text-blue-600">
        {{ $item->judul() }}
    </a>

    <p class="mt-1 flex-1 text-xs text-gray-500">
        {{ $item->subjudul() }}
    </p>

    <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 text-xs">
        <span class="text-gray-500">
            <x-fa name="calendar-days" class="mr-1" />{{ $item->waktuLabel() }}
        </span>

        <span class="font-medium {{ $waktuWarna }}">
            {{ $item->sisaWaktu() }}
        </span>
    </div>
</article>