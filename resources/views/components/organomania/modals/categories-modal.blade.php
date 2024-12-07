@use(App\Enums\RegisterCategory)

@props(['categoriesGroups', 'categoryClass', 'title' => __('Přehled kategorií')])

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
                @foreach ($categoriesGroups as $group => $categories)
                    <div @class(['mb-4' => !$loop->last])>
                        <h3 class="fs-5">{{ __($categoryClass::getGroupName($group)) }}</h3>
                        @foreach ($categories as $category)
                            <div class="mb-2">
                                <a
                                    href="{{ $category->getItemsUrl() }}"
                                    wire:navigate
                                    class="badge text-decoration-none text-bg-{{ $category->getColor() }}"
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
