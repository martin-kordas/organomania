@props(['shortName' => false])

<a
    href="{{ $category->getItemsUrl() }}"
    @if ($newTab) target="_blank" @else wire:navigate @endif
    class="badge text-decoration-none text-bg-{{ $category->getColor() }}"
    data-category-id="{{ $category->getValue() }}"
    @if ($showTooltip)
        data-bs-toggle="tooltip"
        data-bs-title="{{ $category->getDescription() }}"
    @endif
>
    @if (!$slot->isEmpty())
        {{ $slot }}
    @else
        {{ __($shortName ? $category->getShortName() : $category->getName()) }}
    @endif
</a>
