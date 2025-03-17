@props(['quizResults', 'showName' => true])

@use(App\Helpers)
@use(Illuminate\Support\Facades\Auth)

<table class="table table-sm table-hover w-auto" style="min-width: 250px">
    <tr>
        @if ($showName)
            <th>{{ __('Uživatel') }}</th>
        @endif
        <th class="text-end">{{ __('Datum') }}</th>
        <th class="text-end">{{ __('Skóre') }}</th>
    </tr>
    @foreach ($quizResults as $quizResult)
        <tr @class(['table-warning' => $quizResult->user_id === Auth::user()?->id])>
            @if ($showName)
                <td>{{ $quizResult->name ?? '('.__('anonymní').')' }}</td>
            @endif
            <td class="text-end">{{ Helpers::formatDate($quizResult->created_at, true) }}</td>
            <td class="text-end">
                <span class="badge text-bg-info">{{ $quizResult->score }}</span>
            </td>
        </tr>
    @endforeach
</table>