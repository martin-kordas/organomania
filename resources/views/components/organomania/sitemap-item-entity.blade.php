@props(['url', 'lastmod', 'signedUrl' => false])

<url>
    <x-organomania.sitemap-location :$url :$signedUrl />
    <lastmod>{{ $lastmod->format('Y-m-d') }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
</url>
