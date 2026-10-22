@props(['item', 'index'])

<li {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2.5']) }}>
    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold
        {{ $index === 0 ? 'bg-accent text-white' : 'border border-gray-200 bg-white text-gray-500' }}">
        {{ $index + 1 }}
    </span>

    <x-type-badge :type="$item->type" class="shrink-0" />

    <a href="{{ $item->isTugas() ? route('tugas.show', $item->model) : route('agenda.show', $item->model) }}"
       class="min-w-0 flex-1 truncate text-sm font-medium text-gray-800 hover:text-accent">
        {{ $item->judul() }}
    </a>

    @if ($item->priority() !== null)
        <x-priority-badge :priority="$item->priority()" class="hidden sm:inline-block" />
    @endif

    <span class="flex shrink-0 items-center gap-1 text-xs font-medium
        {{ ($item->sisaDetik() ?? 1) < 0
            ? 'text-red-600'
            : ($item->status() === \App\Support\ActivityItem::STATUS_SELESAI ? 'text-emerald-600' : 'text-gray-500') }}">
        <x-fa name="clock" />{{ $item->sisaWaktu() }}
    </span>
</li>
