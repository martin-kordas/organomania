@props(['label', 'icon', 'url', 'active' => false])

<li class="nav-item">
    <a href="{{ $url }}" wire:navigate @class(['nav-link', 'px-2', 'px-xl-3', 'active' => $active])>
        <i class="bi-{{ $icon }}"></i>
        {{ $label }}
    </a>
</li>
