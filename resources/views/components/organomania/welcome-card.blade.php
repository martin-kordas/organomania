@props(['title', 'url', 'imageUrl'])

<div
    class="welcome-card col-lg-4 d-flex"
    data-target-url="{{ $url }}"
    onclick="location.href = this.dataset.targetUrl"
    style="cursor: pointer;"
>
    <div class="position-relative p-3 border border-tertiary rounded h-100 w-100 d-flex align-items-center justify-items-center">
        <div class="w-100">
            <img class="bd-placeholder-img rounded-circle" width="140" height="140" src="{{ $imageUrl }}" />
            <h2 class="fw-normal">{{ $title }}</h2>
            <p>
                {{ $slot }}
            </p>
            @isset($list)
                <ul class="list-unstyled" style="font-size: 90%;">
                    {{ $list }}
                </ul>
            @endisset
            <p class="mb-0">
                <a class="btn btn-secondary" href="{{ $url }}">{{ __('Zobrazit') }} »</a>
            </p>
            @isset($footer)
                {{ $footer }}
            @endisset
        </div>
    </div>
</div>
