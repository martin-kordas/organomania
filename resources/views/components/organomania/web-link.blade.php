@props(['url'])

@php $urlData = explode('°', $url) @endphp
<a class="icon-link icon-link-hover text-decoration-none" target="_blank" href="{{ $urlData[0] }}">
    <i class="bi bi-link-45deg"></i>
    <span class="text-decoration-underline">
        {{ $urlData[1] ?? str($urlData[0])->limit(65) }}
    </span>
    @isset($urlData[2])
        <span class="text-secondary">
            ({{ $urlData[2] }})
        </span>
    @endisset
</a>
