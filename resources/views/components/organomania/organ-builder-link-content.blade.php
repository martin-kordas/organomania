@props(['organBuilder', 'yearBuilt' => null, 'isRebuild' => false, 'showActivePeriod' => null, 'placeholder' => __('neznámý varhanář')])

<span>
    {{ $organBuilder->name ?? $placeholder }}
    @if ($organBuilder && !$organBuilder->isPublic())
        <i class="bi bi-lock text-warning"></i>
    @endif
    <span class="text-secondary">
        @if ($showActivePeriod)
            ({{ $organBuilder->active_period }})
        @elseif ($yearBuilt)
            ({{ $yearBuilt }}@if ($isRebuild), {{ __('přestavba') }}@endif)
        @endif
    </span>
</span>
