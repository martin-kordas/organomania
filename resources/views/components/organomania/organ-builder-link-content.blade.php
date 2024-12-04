@props(['organBuilder', 'yearBuilt' => null, 'isRebuild' => false, 'showActivePeriod' => null, 'placeholder' => __('neznámý varhanář')])

<span>
    {{ $organBuilder->name ?? $placeholder }}
    <span class="text-secondary">
        @if ($showActivePeriod)
            ({{ $organBuilder->active_period }})
        @elseif ($yearBuilt)
            (@if ($isRebuild){{ __('přestavba') }}, @endif{{ $yearBuilt }})
        @endif
    </span>
</span>
