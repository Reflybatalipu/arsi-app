@props(['name'])

<i {{ $attributes->merge(['class' => 'fa-solid fa-'.$name]) }} aria-hidden="true"></i>