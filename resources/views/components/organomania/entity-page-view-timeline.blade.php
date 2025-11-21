@props(['organs', 'thumbnailOrgan', 'thumbnailComponent'])

<p class="text-secondary text-center">
    <small>
        {{ __('Klikněte na bod na časové ose pro zobrazení podrobností.') }}
    </small>
</p>

<div class="container entity-page-timeline">
    <div
        id="timeline"
        class="mb-4"
        data-min="{{ $this->timelineRange[0] }}"
        data-max="{{ $this->timelineRange[1] }}"
        data-scale="{{ $this->timelineScale }}"
        data-step="{{ $this->timelineStep }}"
        data-axis="{{ $this->filterId ? 'bottom' : 'both' }}"
        @if ($this->selectedTimelineEntityType) data-selected-entity-type="{{ $this->selectedTimelineEntityType }}" @endif
        @if ($this->selectedTimelineEntityId) data-selected-entity-id="{{ $this->selectedTimelineEntityId }}" @endif
        @isset ($this->timelineViewRange) data-start="{{ $this->timelineViewRange[0] }}" @endisset
        @isset ($this->timelineViewRange) data-end="{{ $this->timelineViewRange[1] }}" @endisset
        wire:ignore
    ></div>
    
    <x-organomania.modals.organ-thumbnail-modal :showOrgansTimeline="!$this->filterId" />
      
    @if (!$this->filterId && !empty($this->timelineMarkers))
        <h6>{{ __('Vyznačené milníky') }}</h6>
        <ul class="small">
            @foreach ($this->timelineMarkers as $marker)
                <li>
                    <strong>{{ $marker['name'] }}</strong>: {{ $marker['description'] }}
                </li>
            @endforeach
        </ul>
    @endif

    <a class="btn btn-sm btn-outline-secondary" href="{{ route('organ-builders.list-by-age') }}" wire:navigate>
        {{ __('Varhanáři podle věku dožití') }}
    </a>

</div>

@script
<script>
    let timelineItems = {{ Js::from($this->timelineItems) }}
    let timelineGroups = {{ Js::from($this->timelineGroups) }}
    let timelineMarkers = {{ Js::from($this->timelineMarkers) }}
    
    setTimeout(() => {
        initTimeline($wire, timelineItems, timelineGroups, timelineMarkers)
    })
</script>
@endscript
