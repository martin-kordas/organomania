@props(['title', 'url', 'buttonLabel' => __('Zobrazit'), 'icon' => null])

<div {{ $attributes->class(['row', 'align-items-stretch', 'my-3']) }}>
    <div class="organ col-lg-4 mx-auto" style="cursor: pointer;" data-target-url="{{ $url }}" onclick="location.href = this.dataset.targetUrl">
        <div class="position-relative p-2 border border-tertiary rounded h-100 w-100">
            <div class="d-flex">
                <h5 class="me-2 mb-0 mt-0 me-auto align-self-center">
                    @isset($icon)
                        <i class="bi bi-{{ $icon }}"></i>
                    @endisset
                    {{ $title }}
                </h5>
                <p class="mb-0">
                    <a class="btn btn-sm btn-secondary" href="{{ $url }}" wire:navigate>{{ $buttonLabel }} »</a>
                </p>
            </div>
            <small>
                {{ $slot }}
            </small>
        </div>
    </div>
</div>