@use(App\Services\MarkdownConvertorService)

@props(['municipalityInfo'])

<div class="municipality-info mt-3 mb-4 m-auto" style="max-width: 1000px;">
    <h3 class="fs-5 text-center">{{ $municipalityInfo->heading }}</h3>
    @isset ($municipalityInfo->description)
        <div class="markdown small">{!! trim(app(MarkdownConvertorService::class)->convert($municipalityInfo->description)) !!}</div>
    @endisset
</div>