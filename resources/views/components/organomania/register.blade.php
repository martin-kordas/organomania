@props([
    'registerName', 'registerNames', 'register', 'showPitches' => true, 'language' => null, 'pitch' => null,
    'excludeDispositionIds' => [], 'excludeOrganIds' => []
])

@php $language ??= $registerName?->language @endphp

<div>
    <span
        class="badge text-bg-primary"
        @if ($description = $register->registerCategory->getDescription()) data-bs-toggle="tooltip" data-bs-title="{{ $description }}" @endif
    >
        {{ $register->registerCategory->getName() }}
    </span>
    @foreach ($register->registerCategories as $category)
        @php $description = $category->getEnum()->getDescription() @endphp
        <span
            class="badge text-bg-secondary"
            @if ($description) data-bs-toggle="tooltip" data-bs-title="{{ $description }}" @endif
        >
            {{ $category->getEnum()->getName() }}
        </span>
    @endforeach
</div>

@isset($register->description)
    <div class="mt-2">
        {{ $register->description }}
    </div>
    <hr>
@endisset

@isset($pitch)
    <div class="mt-2">
        {{ __('Poloha') }}: <em>{{ $pitch->getLabel($language) }}</em>
        <small class="text-body-secondary">
            ({{ $pitch->getInterval() }} {{ __('poloha') }} &ndash; {{ __('na klávese') }} c<sup>1</sup> {{ __('zní tón') }} {!! $pitch->getAliquoteToneFormatted() !!})
        </small>
    </div>
@endisset

@if ($showPitches)
    <div class="mt-2">
        {{ __('Běžné polohy rejstříku') }}: <em>{{ $register->getPitchesLabels($language)->implode(', ') }}</em>
    </div>
@endif

@php $dispositions = $register->getDispositions($excludeDispositionIds, $excludeOrganIds) @endphp
@if ($dispositions->isNotEmpty())
    <div class="mt-2">
        {{ __('Příklady v dispozicích') }}:
        <ul class="mb-0">
            @foreach ($dispositions as $disposition)
                <li>
                    <a href="{{ route('dispositions.show', [$disposition->slug, 'highlightRegisterId' => $registerName->register_id]) }}" class="link-primary text-decoration-none" target="_blank">
                        {{ $disposition->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
