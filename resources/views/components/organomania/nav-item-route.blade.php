@props(['label', 'icon', 'route', 'routeActive' => null, 'highlightedCount' => null])

@php
    $routeActive ??= $route
@endphp

<x-organomania.nav-item
    :$label
    :$icon
    :$highlightedCount
    :url="route($route)"
    :active="request()->routeIs($routeActive)"
/>
