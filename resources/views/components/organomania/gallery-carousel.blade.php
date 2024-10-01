@props(['images', 'id' => 'galleryCarousel'])

<div id="{{ $id }}" {{ $attributes->class(['gallery-carousel', 'carousel', 'slide', 'rounded', 'border']) }}>
    <div class="carousel-indicators">
        @foreach ($images as $image)
            <button type="button" data-bs-target="#{{ $id }}" data-bs-slide-to="{{ $loop->index }}" class="active" aria-current="true" aria-label="Slide {{ $loop->iteration }}"></button>
        @endforeach
    </div>
    <div class="carousel-inner rounded">
        @foreach ($images as [$src, $credits])
            <div @class(['carousel-item', 'active' => $loop->first])>
                <img src="{{ $src }}" class="d-block m-auto" alt="{{ __('Náhled') }}" @isset($credits) title="{{ __('Licence obrázku') }}: {{ $credits }}" @endisset>
            </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#{{ $id }}" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">{{ __('Předchozí') }}</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#{{ $id }}" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">{{ __('Další') }}</span>
    </button>
</div>
