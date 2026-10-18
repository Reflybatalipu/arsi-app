@props(['item', 'index'])

<li {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2.5']) }}>
    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-xs font-bold
        {{ $index === 0 ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-200 bg-white text-gray-500' }}">
        {{ $index + 1 }}
    </span>

    <x-type-badge :type="$item->type" class="shrink-0" />

    <a href="{{ $item->isTugas() ? route('tugas.show', $item->model) : route('agenda.show', $item->model) }}"
       class="min-w-0 flex-1 truncate text-sm font-medium text-gray-800 hover:text-blue-600">
        {{ $item->judul() }}
    </a>

    @if ($item->priority() !== null)
        <x-priority-badge :priority="$item->priority()" class="hidden sm:inline-block" />
    @endif

    <span class="shrink-0 text-xs font-medium
        {{ ($item->sisaDetik() ?? 1) < 0
            ? 'text-red-600'
            : ($item->status() === \App\Support\ActivityItem::STATUS_SELESAI ? 'text-green-600' : 'text-gray-500') }}">
        {{ $item->sisaWaktu() }}
    </span>
</li>