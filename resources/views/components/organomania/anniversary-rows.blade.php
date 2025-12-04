@props(['entity', 'year'])

<tr wire:key="{{ $entity->id }}">
    <td class="pe-3" rowspan="{{ count($entity->anniversaries) }}">
        {{ $entityLink }}
    </td>
    @php [$anniversaryYear, $anniversaryYears, $anniversary] = $entity->anniversaries[0] @endphp
    <td @class(['table-primary' => $this->shouldHighlightAnniversary($anniversaryYear, $year)])>
        <span class="fw-semibold">{{ $anniversaryYears }}</span>
        {{ $anniversary }}
        <span class="text-secondary">({{ $anniversaryYear }})</span>
    </td>
</tr>
@foreach (array_slice($entity->anniversaries, 1) as [$anniversaryYear, $anniversaryYears, $anniversary])
    <tr>
        <td @class(['table-primary' => $this->shouldHighlightAnniversary($anniversaryYear, $year)])>
            <span class="fw-semibold">{{ $anniversaryYears }}</span>
            {{ $anniversary }}
            <span class="text-secondary">({{ $anniversaryYear }})</span>
        </td>
    </tr>
@endforeach
