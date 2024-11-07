@props(['url', 'lastmod'])

<url>
    <x-organomania.sitemap-location :$url />
    <lastmod>{{ $lastmod->format('Y-m-d') }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
</url>
