@props(['organBuilder', 'yearBuilt' => null, 'isRebuild' => false, 'placeholder' => __('neznámý varhanář')])

<span>
    {{ $organBuilder->name ?? $placeholder }}
    <span class="text-secondary">
        @if ($yearBuilt)
            (@if ($isRebuild){{ __('přestavba') }}, @endif{{ $yearBuilt }})
        @endif
    </span>
</span>
