@props(['name', 'class' => 'h-5 w-5'])

@php
    $icons = [
        'home' => 'fa-solid fa-house',
        'users' => 'fa-solid fa-users',
        'newspaper' => 'fa-regular fa-newspaper',
        'plus' => 'fa-solid fa-plus',
        'tag' => 'fa-solid fa-tag',
        'cog' => 'fa-solid fa-cog',
        'gear' => 'fa-solid fa-gear',
        'calendar-days' => 'fa-solid fa-calendar-days',
        'image' => 'fa-regular fa-image',
        'credit-card' => 'fa-regular fa-credit-card',
        'shopping-cart' => 'fa-solid fa-cart-shopping',
        'circle-info' => 'fa-solid fa-circle-info',
        'handshake' => 'fa-solid fa-handshake-simple',
        'clipboard-list' => 'fa-solid fa-clipboard-list',
    ];

    $iconClass = $icons[(string) ($name ?? '')] ?? 'fa-solid fa-circle';
@endphp

<span class="inline-flex items-center justify-center {{ $class }}" {{ $attributes }}>
    <i class="fa-fw {{ $iconClass }}"></i>
</span>
