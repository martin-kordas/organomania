@props(['title', 'url', 'icon' => 'link-45deg'])

<a href="{{ $url }}" target="_blank" class="list-group-item list-group-item-action link-primary">
    <div>
        <i class="bi bi-{{ $icon }}"></i>
        @if (isset($source))
            <em>{{ $source }}:</em>
        @endif
        {{ $slot }}
    </div>
    @if (isset($description))
        <div class="text-secondary">
            <small>{{ $description }}</small>
        </div>
    @endif
</a>