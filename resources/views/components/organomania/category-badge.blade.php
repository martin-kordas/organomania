<a
    href="{{ $category->getItemsUrl() }}"
    wire:navigate
    class="badge text-decoration-none text-bg-{{ $category->getColor() }}"
    @if ($showTooltip)
        data-bs-toggle="tooltip"
        data-bs-title="{{ $category->getDescription() }}"
    @endif
>{{ __($category->getName()) }}</a>
