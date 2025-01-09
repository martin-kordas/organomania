@props(['organs', 'thumbnailOrgan', 'thumbnailComponent'])

<p class="text-secondary text-center">
    <small>
        {{ __('Klikněte na bod na časové ose pro zobrazení podrobností.') }}
    </small>
</p>

<div class="container entity-page-timeline">
    <div
        id="timeline"
        data-step="{{ $this->timelineStep }}"
        @if ($this->selectedTimelineEntityType) data-selected-entity-type="{{ $this->selectedTimelineEntityType }}" @endif
        @if ($this->selectedTimelineEntityId) data-selected-entity-id="{{ $this->selectedTimelineEntityId }}" @endif
        @if (!$this->filterId) data-axis-both @endif
        @isset ($this->timelineViewRange) data-start="{{ $this->timelineViewRange[0] }}" @endisset
        @isset ($this->timelineViewRange) data-end="{{ $this->timelineViewRange[1] }}" @endisset
        wire:ignore
    ></div>
    
    <x-organomania.modals.organ-thumbnail-modal :showOrgansTimeline="!$this->filterId" />
      
    @if (!$this->filterId)
        <h6 class="mt-4">{{ __('Vyznačené milníky') }}</h6>
        <ul class="small">
            @foreach ($this->timelineMarkers as $marker)
                <li>
                    <strong>{{ $marker['name'] }}</strong>: {{ $marker['description'] }}
                </li>
            @endforeach
        </ul>
    @endif

</div>

@script
<script>
    let timelineItems = {{ Js::from($this->timelineItems) }}
    let timelineGroups = {{ Js::from($this->timelineGroups) }}
    let timelineMarkers = {{ Js::from($this->timelineMarkers) }}
    
    initTimeline($wire, timelineItems, timelineGroups, timelineMarkers)
</script>
@endscript
