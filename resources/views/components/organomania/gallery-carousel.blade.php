@props(['images', 'id' => 'galleryCarousel'])

<div id="{{ $id }}" {{ $attributes->class(['gallery-carousel', 'carousel', 'slide', 'rounded', 'border']) }} data-bs-ride="carousel">
    @if (count($images) > 1)
        <div class="carousel-indicators">
            @foreach ($images as $image)
                <button type="button" data-bs-target="#{{ $id }}" data-bs-slide-to="{{ $loop->index }}" class="active" aria-current="true" aria-label="Slide {{ $loop->iteration }}"></button>
            @endforeach
        </div>
    @endif
    
    <div class="carousel-inner rounded">
        @foreach ($images as $key => [$src, $credits])
            @php 
                $caption = $images[$key][2] ?? null;
                $additional = $images[$key][3] ?? false;
            @endphp
            <div @class(['carousel-item', 'active' => $loop->first]) data-bs-interval="8000">
                <img src="{{ $src }}" class="d-block m-auto" alt="{{ __('Náhled') }}" @isset($credits) title="{{ __('Licence obrázku') }}: {{ $credits }}" @endisset>
                @isset($caption)
                    <div @class(['carousel-caption', 'small', 'text-dark' => $additional, 'text-primary' => !$additional])>
                        <p class="bg-light rounded mb-1 p-1 fst-italic collapsed" style="opacity: 85%" onmousedown="toggleGalleryCaption(this)" ontouchstart="toggleGalleryCaption(this)">{!! $caption !!}</p>
                    </div>
                @endisset
            </div>
        @endforeach
    </div>
    
    @if (count($images) > 1)
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

<script>
    function toggleGalleryCaption(elem) {
        $(elem).toggleClass('collapsed')
    }
</script>