@use(App\Enums\RegisterCategory)

@props(['registerCategoriesGroups'])

<div class="register-categories-modal modal fade" id="registerCategoriesModal" tabindex="-1" data-focus="false" aria-labelledby="registerCategoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                    {{ __('Přehled kategorií rejstříků') }}
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>

            <div class="modal-body">
                @foreach ($registerCategoriesGroups as $group => $categories)
                    <div class="mb-4">
                        <h3 class="fs-5">{{ __(RegisterCategory::getGroupName($group)) }}</h3>
                        @foreach ($categories as $category)
                            @php $showTooltip = $category->getDescription() !== null @endphp
                        
                            <div class="mb-2">
                                <a
                                    href="{{ route('dispositions.registers.index', ['filterCategories' => [$category->value]]) }}"
                                    wire:navigate
                                    @class(['badge', 'text-decoration-none', 'text-bg-primary' => $category->isMain(), 'text-bg-secondary' => !$category->isMain()])
                                >{{ __($category->getName()) }}</a>
                                <br />

                                {{ __($category->getDescription()) }}
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
