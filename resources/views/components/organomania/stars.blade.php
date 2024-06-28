<span {{ $attributes->merge(['class' => 'stars']) }}>
    @for ($i = 0; $i < $countAll; $i++)
        <i class="@if ($i < $count) bi-star-fill @else bi-star @endif text-primary"></i>
    @endfor
    @if ($showCount)
        <span class="text-secondary">({{ $count }}/{{ $countAll }})</span>
    @endif
</span>
