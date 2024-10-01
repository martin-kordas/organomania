@props([
    'categoriesGroups',
    'model' => 'categoryId', 'id' => null, 'allowClear' => false,
])

@php
    use App\Enums\RegisterCategory;

    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="register-category-select form-select form-select-sm select2 @error($model) is-invalid @enderror"
    wire:model.change="{{ $model }}"
    data-placeholder="{{ __('Kategorie rejstříků') }}"
    @if ($allowClear) data-allow-clear="true" @endif
    aria-label="{{ __('Filtr kategorií') }}"
    aria-describedby="{{ "{$id}Feedback" }}"
    multiple
    style="min-width: 18em;"
>
    @foreach ($categoriesGroups as $group => $categories)
        <optgroup label="{{ __(RegisterCategory::getGroupName($group)) }}">
            @foreach ($categories as $category)
                <option title="{{ __($category->getDescription()) }}" value="{{ $category->value }}">
                    {{ __($category->getName()) }}
                </option>
            @endforeach
        </optgroup>
    @endforeach
</select>

@error($model)
    <div id="{{ "{$id}Feedback" }}" class="invalid-feedback">{{ $message }}</div>
@enderror
