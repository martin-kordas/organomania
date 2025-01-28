@props(['url', 'signedUrl' => false])

<loc>{{ $url }}</loc>

{{-- je-li URL signed, přidáním parametru lang by se podpis narušil --}}
@if (!$signedUrl)
    @foreach (['cs', 'en'] as $locale)
        <xhtml:link
            rel="alternate"
            hreflang="{{ $locale }}"
            href="{{ $url }}?lang={{ $locale }}" />
    @endforeach
@endif