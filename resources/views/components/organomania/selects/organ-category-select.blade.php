@props([
    'categoriesGroups', 'customCategoriesGroups', 'placeholder',
    'counts' => true, 'alwaysShowCustomCategories' => false,
    'model' => 'categoryId', 'id' => null, 'allowClear' => false, 'live' => false
])

@php
    use App\Enums\OrganCategory;
    use App\Models\OrganCustomCategory;

    $id ??= $model;
    $modelAttribute = $live ? "model.live" : "model";
    $customCategoryExists = !empty(Arr::flatten($customCategoriesGroups));
@endphp

<select
    id="{{ $id }}"
    class="organ-category-select form-select select2 @error($model) is-invalid @enderror"
    wire:{{ $modelAttribute }}="{{ $model }}"
    data-placeholder="{{ $placeholder }}"
    @if ($allowClear) data-allow-clear="true" @endif
    aria-label="{{ __('Filtr kategorií') }}"
    aria-describedby="{{ "{$id}Feedback" }}"
    multiple
>
    @if ($customCategoryExists && (Gate::allows('useOrganCustomCategories') || $alwaysShowCustomCategories))
        @foreach ($customCategoriesGroups as $group => $categories)
            <optgroup wire:key="{{ $group }}" label="{{ $this->getCustomCategoryGroupName($group) }}">
                @foreach ($categories as $category)
                    @php $organsCount = $counts ? $this->getOrganCustomCategoryOrganCount($category) : null; @endphp
                    <option wire:key="{{ $category->getValue() }}" value="custom-{{ $category->getValue() }}" title="{{ $category->getDescription() }}">
                        {{ __($category->getName()) }}
                        @if ($counts && $organsCount > 0)
                            ({{ $organsCount }})
                        @endif
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    @endif

    @foreach ($categoriesGroups as $group => $categories)
        <optgroup wire:key="{{ $group }}" label="{{ __(OrganCategory::getGroupName($group)) }}">
            @foreach ($categories as $category)
                @php $organsCount = $counts ? $this->getOrganCategoryOrganCount($category) : null; @endphp
                <option wire:key="{{ $category->getValue() }}" title="{{ __($category->getDescription()) }}" value="{{ $category->value }}">
                    {{ __($category->getName()) }}
                    @if ($counts && $organsCount > 0)
                        ({{ $organsCount }})
                    @endif
                </option>
            @endforeach
        </optgroup>
    @endforeach
</select>

@error($model)
    <div id="{{ "{$id}Feedback" }}" class="invalid-feedback">{{ $message }}</div>
@enderror
