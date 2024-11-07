@props(['url'])

<loc>{{ $url }}</loc>
@foreach (['cs', 'en'] as $locale)
    <xhtml:link
        rel="alternate"
        hreflang="{{ $locale }}"
        href="{{ $url }}?lang={{ $locale }}" />
@endforeach
