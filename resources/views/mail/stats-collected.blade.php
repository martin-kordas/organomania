<div>
    Byly vygenerovány statistiky pro aplikaci {{ config('app.name') }} za den {{ $stats->computed_on->format('Y-m-d') }}.
    
    <dl>
        <dt>Počet uživatelů</dt>
        <dd>{{ Number::format($stats->users_count) }}</dd>
        
        <dt>Počet varhan</dt>
        <dd>{{ Number::format($stats->organs_count) }}</dd>
        
        <dt>Počet varhanářů</dt>
        <dd>{{ Number::format($stats->organ_builders_count) }}</dd>
        
        <dt>Počet vlastních kategorií varhan</dt>
        <dd>{{ Number::format($stats->organ_custom_categories_count) }}</dd>
        
        <dt>Počet oblíbených varhan</dt>
        <dd>{{ Number::format($stats->organ_likes_count) }}</dd>
        
        <dt>Nejoblíbenější varhany</dt>
        <dd>
            {{ $organLikesMaxOrgan->municipality }}, {{ $organLikesMaxOrgan->place }}
            ({{ Number::format($stats->organ_likes_max) }}&times;)
        </dd>
        
        <dt>Průměrná obliba varhan</dt>
        <dd>{{ Number::format($stats->organ_likes_avg) }}</dd>
    </dl>
</div>
