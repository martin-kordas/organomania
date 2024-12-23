@props(['url', 'limit' => 65])

@php
    $urlData = array_map(trim(...), explode('°', $url));
    $displayedUrl = str($urlData[0])->replaceMatches('/^https?\:\/\//', '')->limit(65);
@endphp

<a class="icon-link icon-link-hover align-items-start text-decoration-none" target="_blank" href="{{ $urlData[0] }}">
    <i class="bi bi-link-45deg"></i>
    <span>
        <span class="text-decoration-underline">{{ $urlData[1] ?? $displayedUrl }}</span>
        @isset($urlData[2])
            <span class="text-secondary">({{ $urlData[2] }})</span>
        @endisset
    </span>
</a>
