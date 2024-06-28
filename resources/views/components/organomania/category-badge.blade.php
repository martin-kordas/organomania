<span
    class="badge text-bg-{{ $category->getColor() }}"
    @if ($showTooltip)
        data-bs-toggle="tooltip"
        data-bs-title="{{ $category->getDescription() }}"
    @endif
>{{ __($category->getName()) }}</span>
