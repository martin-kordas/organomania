@props(['organs'])

<div>
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-4 align-items-center">
        @foreach ($organs as $organ)
            <div class="col" wire:key="organ-{{ $organ->id }}">
                <x-dynamic-component :component="$this->thumbnailComponent" :$organ />
            </div>
        @endforeach
    </div>
</div>
