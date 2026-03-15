@props(['organBuilderAdditionalImage', 'name' => null, 'newTab' => false])

<a
    class="additional-image-link icon-link icon-link-hover align-items-start link-primary text-decoration-none"
    href="{{ $organBuilderAdditionalImage->getViewUrl() }}"
    @if ($newTab) target="_blank" @else wire:navigate @endif
>
    <span>
        {{ $name ?? $organBuilderAdditionalImage->name }}
    </span>
</a>
