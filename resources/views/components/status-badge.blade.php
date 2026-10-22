@props(['status'])

@if ($status === \App\Support\ActivityItem::STATUS_SELESAI)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-md bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700']) }}>
        <x-fa name="circle-check" />Selesai
    </span>
@elseif ($status === \App\Support\ActivityItem::STATUS_LEWAT)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-md bg-red-100 px-2 py-0.5 text-[11px] font-medium text-red-700']) }}>
        <x-fa name="triangle-exclamation" />Lewat waktu
    </span>
@endif
