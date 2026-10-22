@props(['item', 'index'])

<li {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-lg border border-line bg-surface-2 px-3 py-2.5']) }}>
    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold
        {{ $index === 0 ? 'bg-accent text-on-accent' : 'border border-line bg-surface text-ink-3' }}">
        {{ $index + 1 }}
    </span>

    <x-type-badge :type="$item->type" class="shrink-0" />

    <a href="{{ $item->isTugas() ? route('tugas.show', $item->model) : route('agenda.show', $item->model) }}"
       class="min-w-0 flex-1 truncate text-sm font-medium text-ink hover:text-accent">
        {{ $item->judul() }}
    </a>

    @if ($item->priority() !== null)
        <x-priority-badge :priority="$item->priority()" class="hidden sm:inline-block" />
    @endif

    <span class="flex shrink-0 items-center gap-1 text-xs font-medium
        {{ ($item->sisaDetik() ?? 1) < 0
            ? 'text-red-600 dark:text-red-400'
            : ($item->status() === \App\Support\ActivityItem::STATUS_SELESAI ? 'text-emerald-600 dark:text-emerald-400' : 'text-ink-3') }}">
        <x-fa name="clock" />{{ $item->sisaWaktu() }}
    </span>
</li>
