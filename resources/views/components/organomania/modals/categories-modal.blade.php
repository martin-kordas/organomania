@use(App\Enums\RegisterCategory)
@use(App\Enums\OrganCategory)
@use(App\Enums\OrganBuilderCategory)

@props(['categoriesGroups', 'categoryClass'])

@php
    if ($categoryClass === OrganCategory::class) {
        $title = __('Přehled kategorií varhan');
        $highlightHint = __('Kategorie přiřazené aktuálním varhanám jsou zvýrazněny žlutě.');
    }
    if ($categoryClass === RegisterCategory::class) {
        $title = __('Přehled kategorií rejstříků');
        $highlightHint = __('Kategorie přiřazené aktuálnímu rejstříku jsou zvýrazněny žlutě.');
    }
    else {
        $title = __('Přehled kategorií varhanářů');
        $highlightHint = __('Kategorie přiřazené aktuálnímu varhanáři jsou zvýrazněny žlutě.');
    }
@endphp

<div class="categories-modal modal fade" id="categoriesModal" tabindex="-1" data-focus="false" aria-labelledby="categoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-4">
                    {{ $title }}
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>

            <div class="modal-body">
                <div class="highlight-hint d-none mb-3">
                    <small class="text-secondary">{{ $highlightHint }}</small>
                </div>
                @foreach ($categoriesGroups as $group => $categories)
                    <div @class(['mb-4' => !$loop->last])>
                        <h3 class="fs-5 mb-3">{{ __($categoryClass::getGroupName($group)) }}</h3>
                        @foreach ($categories as $category)
                            <div class="category-info position-relative border border-tertiary mb-2 p-2 rounded" data-category-id="{{ $category->value }}">
                                <a
                                    href="{{ $category->getItemsUrl() }}"
                                    wire:navigate
                                    class="badge stretched-link text-decoration-none text-bg-{{ $category->getColor() }}"
                                >
                                    {{ __($category->getName()) }}
                                </a>
                                <br />

                                @if (!$category->isPeriodCategory())
                                    {{ __($category->getDescription()) }}
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    window.highlightCategoriesInModal = function (categoryIds) {
        $('.categories-modal .category-info').filter(function () {
            let categoryId = parseInt($(this).data('categoryId'))
            return categoryIds.includes(categoryId)
        }).addClass('bg-warning-subtle')
            
        $('.categories-modal .highlight-hint').removeClass('d-none');
    }
        
    var initModal = function () {
        setTimeout(() => {
            $('.categories-modal').each(function () {
                this.addEventListener('hidden.bs.modal', () => {
                    $('.categories-modal .category-info.bg-warning-subtle').removeClass('bg-warning-subtle')
                    $('.categories-modal .highlight-hint').addClass('d-none');
                })
            })
        })
    }
     
    // initModal voláno 2x kvůli bugům při tlačítku Zpět
    document.addEventListener('livewire:navigated', initModal)
    $wire.on('bootstrap-rendered', initModal)
</script>
@endscript