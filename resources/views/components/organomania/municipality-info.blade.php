@use(App\Services\MarkdownConvertorService)

@props(['municipalityInfo'])

<div class="municipality-info text-center mt-3 mb-4">
    <h3 class="fs-5">{{ $municipalityInfo->heading }}</h3>
    @isset ($municipalityInfo->description)
        <div class="markdown">{!! trim(app(MarkdownConvertorService::class)->convert($municipalityInfo->description)) !!}</div>
    @endisset
</div>