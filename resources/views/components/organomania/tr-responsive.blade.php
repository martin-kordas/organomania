@props(['title', 'small' => true, 'modifyHtmlCb' => null])

@php
    // modifikace HTML může být potřeba např. pro ošetření duplicitních ID
    $content2 = $slot->toHtml();
    if (is_callable($modifyHtmlCb)) $content2 = $modifyHtmlCb($content2);
@endphp

<tr class="d-none d-md-table-row">
    <th>{{ $title }}</th>
    <td @class(['small' => $small])>
        {{ $slot }}
    </td>
</tr>
<tr class="d-md-none">
    <td colspan="2">
        <strong class="fw-semibold">{{ $title }}</strong>
        <br />
        <div @class(['small' => $small])>
            {!! $content2 !!}
        </div>
    </td>
</tr>
