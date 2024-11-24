@props(['label', 'icon', 'url', 'active' => false, 'highlightedCount' => null])

<li class="nav-item">
    <a href="{{ $url }}" wire:navigate @class(['nav-link', 'py-1', 'px-sm-2', 'position-relative', 'active' => $active])>
        <i class="bi-{{ $icon }} d-xl-none d-xxl-inline"></i>
        {{ $label }}
        @if ($highlightedCount)
            <span class="info-count-badge position-absolute top-0 start-100 translate-middle">
                <span class="badge rounded-pill text-bg-danger" style="font-size: 55%;">
                    {{ $highlightedCount }}
                </span>
            </span>
        @endif
    </a>
</li>
