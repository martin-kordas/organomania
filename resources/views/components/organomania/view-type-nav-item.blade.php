@props(['viewType'])

<li class="nav-item mx-1">
    <a @class(['nav-link', 'py-1', 'active' => $this->viewType === $viewType]) href="#" onclick="setViewType({{ Js::from($viewType) }})">
        {{ $slot }}
    </a>
</li>
