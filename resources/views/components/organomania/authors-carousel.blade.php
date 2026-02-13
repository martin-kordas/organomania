@props(['authors', 'id' => 'authorsCarousel'])

<div id="{{ $id }}" {{ $attributes->class(['authors-carousel', 'carousel', 'slide', 'rounded', 'border']) }} data-bs-ride="carousel" data-bs-touch="false">
    @if (count($authors) > 1)
        <div class="carousel-indicators p-0 mb-1">
            @foreach ($authors as $author)
                <button type="button" data-bs-target="#{{ $id }}" data-bs-slide-to="{{ $loop->index }}" @class(['active' => $loop->first]) aria-current="true" aria-label="Slide {{ $loop->iteration }}"></button>
            @endforeach
        </div>
    @endif

    <div class="carousel-inner">
        @foreach ($authors as $author)
            <div @class(['carousel-item', 'justify-content-center', 'active' => $loop->first]) data-bs-interval="10000">
                <div class="d-flex align-items-center">
                    <div class="content d-flex flex-column align-items-center text-center p-3">
                        <h5 class="mb-1 fs-6">
                            {{ $author->full_name }}
                            @isset ($author->lifeData)
                                <span class="text-muted fw-normal mb-2">({{ $author->lifeData }})</span>
                            @endisset
                        </h5>

                        <div class="px-md-5 mb-3">
                            <p class="mb-0 fst-italic">{{ $author->description }}</p>
                        </div>

                        <div>
                            @if ($author->cv_url)
                                <div class="mb-2"">
                                    <a href="{{ $author->cv_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        {{ __('Životopis') }}
                                    </a>
                                </div>
                            @endif

                            <div>
                                <a type="button" class="btn btn-outline-primary btn-sm" href="{{ route('publications.index', ['filterAuthorId' => $author->id]) }}" wire:navigate>
                                    <i class="bi bi-book"></i> {{ __('Literatura') }}
                                    <span class="badge rounded-pill text-bg-primary ms-1">{{ $author->publications_count }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if (count($authors) > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#{{ $id }}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('Předchozí') }}</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#{{ $id }}" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('Další') }}</span>
        </button>
    @endif
</div>

<style>
    .authors-carousel {
        max-width: 27em;
        margin: auto;
        background: var(--header-footer-background-light);
        border-color: var(--header-footer-border-color) !important;
    }

    .authors-carousel .carousel-control-prev,
    .authors-carousel .carousel-control-next {
        width: 10%;
    }

    .authors-carousel .carousel-control-prev-icon,
    .authors-carousel .carousel-control-next-icon {
        filter: invert(1) grayscale(100) brightness(0);
    }

    .authors-carousel .carousel-indicators [data-bs-target] {
        background-color: #000;
        margin-bottom: 0.5rem;
    }

    .authors-carousel .carousel-item {
        min-height: 225px;
    }
    .authors-carousel .carousel-item.active,
    .authors-carousel .carousel-item.carousel-item-next,
    .authors-carousel .carousel-item.carousel-item-prev {
        display: flex;
    }
    .authors-carousel .content {
        position: relative;
        top: -0.75rem;
    }
</style>
