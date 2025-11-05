@props(['url', 'lastmod' => null, 'signedUrl' => false])

<url>
    <x-organomania.sitemap-location :$url :$signedUrl />
    @isset($lastmod)
        <lastmod>{{ $lastmod->format('Y-m-d') }}</lastmod>
    @endisset
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
</url>
