@use(Illuminate\Support\Facades\URL)

@props(['organs', 'privateOrgans', 'organBuilders', 'dispositions', 'registerNames', 'festivals', 'competitions'])

<?xml version="1.0" encoding="UTF-8" ?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xhtml="http://www.w3.org/1999/xhtml"
>
    <url>
        <x-organomania.sitemap-location :url="route('welcome')" />
        <changefreq>monthly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <x-organomania.sitemap-location :url="route('dispositions.index')" />
        <loc>{{ route('dispositions.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <x-organomania.sitemap-location :url="route('dispositions.registers.index')" />
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <x-organomania.sitemap-location :url="route('dispositions.diff')" />
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <x-organomania.sitemap-location :url="route('organists.index')" />
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    
    @foreach (['organs', 'organ-builders', 'dispositions', 'festivals', 'competitions'] as $entityType)
        @php($url = route("$entityType.index", ['perPage' => 300]))
        <url>
            <loc>{{ $url }}</loc>
            @foreach (['cs', 'en'] as $locale)
                <xhtml:link
                    rel="alternate"
                    hreflang="{{ $locale }}"
                    href="{{ $url }}&amp;lang={{ $locale }}" />
            @endforeach
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
    
    @foreach ($organs as $organ)
        <x-organomania.sitemap-item-entity
            :url="route('organs.show', $organ->slug)"
            :lastmod="$organ->updated_at"
        />
    @endforeach
    @foreach ($privateOrgans as $organ)
        <x-organomania.sitemap-item-entity
            :url="url(URL::signedRoute('organs.show', $organ->slug, absolute: false))"
            :lastmod="$organ->updated_at"
            :signedUrl="true"
        />
    @endforeach
    @foreach ($organBuilders as $organBuilder)
        <x-organomania.sitemap-item-entity
            :url="route('organ-builders.show', $organBuilder->slug)"
            :lastmod="$organBuilder->updated_at"
        />
    @endforeach
    @foreach ($dispositions as $disposition)
        <x-organomania.sitemap-item-entity
            :url="route('dispositions.show', $disposition->slug)"
            :lastmod="$disposition->updated_at"
        />
    @endforeach
    @foreach ($registerNames as $registerName)
        <x-organomania.sitemap-item-entity
            :url="route('dispositions.registers.show', $registerName->slug)"
            :lastmod="$registerName->updated_at"
        />
    @endforeach
    @foreach ($festivals as $festival)
        <x-organomania.sitemap-item-entity
            :url="route('festivals.show', $festival->slug)"
            :lastmod="$festival->updated_at"
        />
    @endforeach
    @foreach ($competitions as $competition)
        <x-organomania.sitemap-item-entity
            :url="route('competitions.show', $competition->slug)"
            :lastmod="$competition->updated_at"
        />
    @endforeach
</urlset>
