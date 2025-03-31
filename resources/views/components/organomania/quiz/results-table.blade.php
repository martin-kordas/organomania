@props(['quizResults', 'showName' => true, 'showTime' => false, 'highlightFirst' => false, 'sortByName' => false])

@use(App\Helpers)
@use(Illuminate\Support\Facades\Auth)

@php
    $userId = Auth::user()?->id;
@endphp

<table class="table table-sm table-hover w-auto" style="min-width: min(275px, 100%)">
    <tr>
        @if ($showName)
            <th>{{ __('Uživatel') }}</th>
        @endif
        <th class="text-end">
            {{ __('Datum') }}
            @if ($showTime)
                {{ __('a čas') }}
            @endif
            @if ($sortByName)
                <i class="bi-sort-numeric-down-alt"></i>
            @endif
        </th>
        <th class="text-end">
            {{ __('Skóre') }}
            @if (!$sortByName)
                <i class="bi-sort-numeric-down-alt"></i>
            @endif
        </th>
    </tr>
    @foreach ($quizResults as $quizResult)
        @php
            $highlight = (
                $showName && $userId && $quizResult->user_id === $userId
                || $highlightFirst && $loop->first
            );
        @endphp
        <tr @class(['table-warning' => $highlight])>
            @if ($showName)
                <td>
                    @if (!isset($quizResult->name))
                        <span class="text-body-secondary">{{ __('anonymní') }}</span>
                    @else
                        {{ $quizResult->name }}
                    @endif
                </td>
            @endif
            <td class="text-end">
                @if ($showTime)
                    {{ Helpers::formatDateTime($quizResult->created_at, monthNumber: true, seconds: false) }}
                @else
                    {{ Helpers::formatDate($quizResult->created_at, monthNumber: true) }}
                @endif
            </td>
            <td class="text-end">
                <span class="badge text-bg-info">{{ $quizResult->score }}</span>
            </td>
        </tr>
    @endforeach
</table>