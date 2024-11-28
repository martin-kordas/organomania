<span {{ $attributes->merge(['class' => 'stars']) }} style="cursor: default;">
    <span class="graphical">
        @for ($i = 0; $i < $countAll; $i++)
            <i class="@if ($i < $count) bi-star-fill @else bi-star @endif text-info"></i>
        @endfor
        @if ($showCount)
            <span class="text-secondary">({{ $count }}/{{ $countAll }})</span>
        @endif
    </span>
    <span class="text d-none">
        {{ $count }}/{{ $countAll }}
    </span>
</span>
