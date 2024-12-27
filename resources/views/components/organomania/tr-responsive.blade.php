@props(['title'])

<tr class="d-none d-md-table-row">
    <th>{{ $title }}</th>
    <td>{{ $slot }}</td>
</tr>
<tr class="d-md-none">
    <td colspan="2">
        <strong>{{ $title }}</strong>
        <br />
        {{ $slot }}
    </td>
</tr>
