@props(['status'])

@if ($status === \App\Support\ActivityItem::STATUS_SELESAI)
    <span {{ $attributes->merge(['class' => 'rounded bg-green-100 px-2 py-0.5 text-[11px] font-medium text-green-700']) }}>
        Selesai
    </span>
@elseif ($status === \App\Support\ActivityItem::STATUS_LEWAT)
    <span {{ $attributes->merge(['class' => 'rounded bg-red-100 px-2 py-0.5 text-[11px] font-medium text-red-700']) }}>
        Lewat waktu
    </span>
@endif