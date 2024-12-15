@props(['url', 'limit' => 65])

@php
    $urlData = array_map(trim(...), explode('°', $url));
    $urlData[0] = str($urlData[0])->replaceMatches('/^https?\:\/\//', '');
@endphp

<a class="icon-link icon-link-hover align-items-start text-decoration-none" target="_blank" href="{{ $urlData[0] }}">
    <i class="bi bi-link-45deg"></i>
    <span>
        <span class="text-decoration-underline">{{ $urlData[1] ?? str($urlData[0])->limit(65) }}</span>
        @isset($urlData[2])
            <span class="text-secondary">({{ $urlData[2] }})</span>
        @endisset
    </span>
</a>
