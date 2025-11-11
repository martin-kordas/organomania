@props(['url', 'signedUrl' => false])

<loc>{{ $url }}</loc>

{{-- je-li URL signed, přidáním parametru lang by se podpis narušil --}}
@if (!$signedUrl)
    @foreach (['cs', 'en'] as $locale)
        @php $langUrl = url()->query($url, ['lang' => $locale]) @endphp
        <xhtml:link
            rel="alternate"
            hreflang="{{ $locale }}"
            href="{{ $langUrl }}"
        />
    @endforeach
@endif