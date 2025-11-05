@props([
    'organBuilders', 'model' => 'organBuilderId',
    'id' => null, 'select2' => true, 'allowClear' => false, 'small' => false,
    'disabled' => false, 'showActivePeriod' => true, 'multiple' => false, 'live' => false, 'counts' => false
])

@use(App\Models\OrganBuilder)

@php
    $id ??= $model;
    $modelAttribute = $live ? "model.live" : "model";
@endphp

<select
    id="{{ $id }}"
    class="form-select @if ($select2) select2 @endif @error($model) is-invalid @enderror @if ($small) form-select-sm @endif"
    aria-label="{{ __('Filtr varhanářů') }}"
    wire:{{ $modelAttribute }}="{{ $model }}"
    data-placeholder="{{ __('Zvolte varhanáře') }}&hellip;"
    @if ($allowClear) data-allow-clear="true" @endif
    @disabled($disabled)
    aria-describedby="{{ "{$id}Feedback" }}"
    @if ($multiple) multiple @endif
>
    {{-- při vymazání multiple selectu křízkem se chybně vyplňuje prázdná option --}}
    @if (!$multiple)
        <option></option>
    @endif

    @foreach ($organBuilders as $organBuilder)
        @php $organsCount = $counts ? $this->getOrganBuilderOrganCount($organBuilder) : null; @endphp
        @if ($organBuilder->id !== OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED && $organsCount !== 0)
            <option wire:key="{{ $organBuilder->id }}" value="{{ $organBuilder->id }}">
                {{ $organBuilder->name }}
                @if ($showActivePeriod && $organBuilder->active_period)
                    ({{ $organBuilder->active_period }})
                @endif
                @if ($counts && $organsCount > 0)
                    ({{ $organsCount }})
                @endif
                @if (!$organBuilder->isPublic())
                    &#128274;
                @endif
            </option>
        @endif
    @endforeach
</select>

@error($model)
    <div id="{{ "{$id}Feedback" }}" class="invalid-feedback">{{ $message }}</div>
@enderror
