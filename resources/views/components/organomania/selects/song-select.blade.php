@props([
    'songGroups', 'model' => 'songId', 'id' => null, 'select2' => true, 'allowClear' => false, 'multiple' => false,
    'live' => false, 'small' => false, 'frequencyInSelection' => false, 'placeholder' => __('Zvolte píseň')
])

@use(App\Enums\KancionalSongCategory)

@php
    $id ??= $model;
    $modelAttribute = $live ? "model.live" : "model";
@endphp

<select
    id="{{ $id }}"
    class="song-select form-select @if ($small) form-select-sm @endif @if ($select2) select2-songs @endif @error($model) is-invalid @enderror"
    aria-label="{{ __('Výběr písně') }}"
    wire:{{ $modelAttribute }}="{{ $model }}"
    data-placeholder="{{ $placeholder }}"
    @if ($allowClear) data-allow-clear="true" @endif
    @if ($multiple) multiple @endif
    @if ($frequencyInSelection) data-frequency-in-selection @endif
    aria-describedby="{{ "{$id}Feedback" }}"
>
    <option></option>
    @php $categoryNo = 1 @endphp
    @foreach ($songGroups as $songCategoryId => $songs)
        @php
            $songNo = 1;
            $category = KancionalSongCategory::from($songCategoryId);
            $categoryNoStr = str($categoryNo)->padLeft(2, '0');
        @endphp
        <optgroup label="{{ $category->getName() }}" data-sort-string="{{ $categoryNoStr }}-0" wire:key="{{ $categoryNo }}">
            @foreach ($songs as $song)
                <option
                    value="{{ $song->id }}"
                    data-number="{{ $song->number }}"
                    data-purpose="{{ $song->purpose }}"
                    data-color="{{ $category->getColor() }}"
                    data-background="{{ $category->getBackground() }}"
                    data-sort-string="{{ $categoryNo }}-{{ $songNo++ }}"
                    data-worship-songs-count="{{ $song->worship_songs_count }}"
                    data-worship-songs-month-count="{{ $song->worship_songs_month_count }}"
                    wire:key="{{ $categoryNoStr }}-{{ $songNo }}"
                >{{ $song->name }}</option>
            @endforeach
        </optgroup>
        @php $categoryNo++ @endphp
    @endforeach
</select>

@error($model)
    <div id="{{ "{$id}Feedback" }}" class="invalid-feedback">{{ $message }}</div>
@enderror
