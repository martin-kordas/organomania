@props(['viewType'])

<li class="nav-item mx-1">
    <a @class(['nav-link', 'active' => $this->viewType === $viewType]) href="#" @click="$wire.set('viewType', '{{ $viewType }}')">
        {{ $slot }}
    </a>
</li>
