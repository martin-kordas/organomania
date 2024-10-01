@props(['label', 'icon', 'route', 'routeActive' => null])

@php
    $routeActive ??= $route
@endphp

<x-organomania.nav-item
    :$label
    :$icon
    :url="route($route)"
    :active="request()->routeIs($routeActive)"
/>
