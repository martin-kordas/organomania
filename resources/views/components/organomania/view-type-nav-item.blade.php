@props(['viewType'])

<li class="nav-item mx-1">
    <a @class(['nav-link', 'py-1', 'active' => $this->viewType === $viewType]) href="#" wire:click="setViewType('{{ $viewType }}')">
        {{ $slot }}
    </a>
</li>
