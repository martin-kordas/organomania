@props(['title'])

<tr class="d-none d-md-table-row">
    <th>{{ $title }}</th>
    <td class="small">{{ $slot }}</td>
</tr>
<tr class="d-md-none">
    <td colspan="2">
        <strong class="fw-semibold">{{ $title }}</strong>
        <br />
        <div class="small">
            {{ $slot }}
        </div>
    </td>
</tr>
