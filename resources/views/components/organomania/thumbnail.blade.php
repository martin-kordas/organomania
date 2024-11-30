@props(['organ' => null, 'modal' => false])

@php
    if (isset($organ->perex)) $description = $organ->perex;
    elseif (isset($organ->description)) $description = str($organ->description)->limit(215);
    else $description = null;

    $image = $organ?->getThumbnailImage();
@endphp

<div class="organ-thumbnail card shadow-sm m-auto placeholder-glow overflow-hidden">
    
    {{-- obrázek --}}
    @isset($organ)
        <div wire:loading.remove wire:target="setThumbnailOrgan" @class(['image-container', 'position-relative', 'bg-light' => !isset($organ->image_url)])>
            @if ($modal)
                <button type="button" class="btn-close position-absolute end-0 m-2 z-1" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            @endif
            @isset($image)
                <a href="{{ $this->getViewUrl($organ) }}" wire:navigate>
                    <img class="organ-image" src="{{ $image['image_url'] }}" @isset($image['image_credits']) title="{{ __('Licence obrázku') }}: {{ $image['image_credits'] }}" @endisset />
                </a>
            @endisset
            @isset($organ->region_id)
                <a href="{{ $this->getViewUrl($organ) }}" wire:navigate>
                    <img width="125" @class(['region', 'start-0', 'm-2', 'bottom-0', 'position-absolute' => isset($image), 'bg-light' => !isset($image)]) src="{{ Vite::asset("resources/images/regions/{$organ->region_id}.png") }}" />
                </a>
            @endisset
        </div>
    @endisset
    <div wire:loading wire:target="setThumbnailOrgan" class="image-placeholder" style="height: 200px;"></div>
    
    <div class="card-header">
        @isset($organ)
            <div wire:loading.remove wire:target="setThumbnailOrgan">
                {{ $header }}
            </div>
        @endisset
        <div wire:loading.block wire:target="setThumbnailOrgan">
            <h5 class="card-title placeholder w-75"></h5>
            <span class="mb-1 placeholder d-block w-50"></span>
            <span class="placeholder d-block w-25"></span>
        </div>
    </div>
    
    <div class="card-body">
        @isset($organ)
            <div wire:loading.remove wire:target="setThumbnailOrgan" class="list-group-item">
                @if ($slot->isEmpty())
                    @php $categoryExists = false; @endphp
                    @foreach ($organ->{$this->customCategoriesRelation} as $category)
                        <x-organomania.category-badge :category="$category" wire:key="customCategory-{{ $category->id }}" />
                        @php $categoryExists = true; @endphp
                    @endforeach
                    @foreach ($organ->{$this->categoriesRelation} as $category)
                        @if (!$category->getEnum()->isPeriodCategory())
                            <x-organomania.category-badge :category="$category->getEnum()" wire:key="category-{{ $category->id }}" />
                            @php $categoryExists = true; @endphp
                        @endif
                    @endforeach
                    @isset($description)
                        <p @class(['card-text', 'mt-2' => $categoryExists])>{{ $description }}</p>
                    @elseif (!$categoryExists)
                        <small class="card-text text-secondary">{{ __('Pro více informací klikněte na tlačítko Zobrazit.') }}</small>
                    @endisset
                @else
                    {{ $slot }}
                @endif
            </div>
        @endisset
        <div wire:loading.block wire:target="setThumbnailOrgan">
            <span class="placeholder col-3 bg-primary"></span>
            <span class="placeholder col-6 bg-primary"></span>
            <span class="placeholder col-4 bg-primary"></span>
            <div class="w-100"></div>
            <p class="mt-2">
                <span class="placeholder col-7"></span>
                <span class="placeholder col-4"></span>
                <span class="placeholder col-4"></span>
                <span class="placeholder col-6"></span>
                <span class="placeholder col-8"></span>
            </p>
        </div>
    </div>
    
    @if ($this->showThumbnailFooter)
        <div class="card-footer text-body-secondary">
            <div class="d-flex justify-content-between align-items-center gap-2">
                <div class="btn-group">
                    <a type="button" @class(['btn', 'btn-sm', 'btn-primary']) href="{{ isset($organ) ? $this->getViewUrl($organ) : '#' }}" wire:navigate wire:loading.class="disabled" wire:target="setThumbnailOrgan">
                        <i class="bi-eye"></i> <span class="d-none d-sm-inline">{{ __('Zobrazit') }}</span>
                    </a>

                    @if (!$modal)
                        {{-- TODO: má zde opravdu být 'view'? --}}
                        @can([$this->gateUseCustomCategories, 'view'], $organ)
                            <button type="button" class="btn btn-sm btn-outline-primary z-1" data-bs-toggle="modal" data-bs-target="#customCategoriesModal" wire:click="setEditCustomCategoriesOrgan({{ $organ->id }})">
                                <span data-bs-toggle="tooltip" data-bs-title="{{ __('Vlastní kategorie') }}">
                                    <i class="bi-tag"></i>
                                    <span class="d-none d-xxl-inline">{{ __('Kategorie') }}</span>
                                    @if ($organ->{$this->customCategoriesCountProp} > 0)
                                        <span class="badge text-bg-info rounded-pill">{{ $organ->{$this->customCategoriesCountProp} }}</span>
                                    @endif
                                </span>
                            </button>
                        @endcan

                        @can('update', $organ)
                            <a type="button" class="btn btn-sm btn-outline-primary z-1" href="{{ route($this->editRoute, $organ->id) }}" wire:navigate data-bs-toggle="tooltip" data-bs-title="{{ __('Upravit') }}">
                                <i class="bi-pencil"></i>
                            </a>
                        @endcan
                    @endif
                </div>
                @if (!$modal)
                    <span data-bs-toggle="tooltip" data-bs-title="{{ __('Sdílet') }}">
                        <button type="button" class="btn btn-sm btn-outline-primary z-1" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $this->getShareUrl($organ) }}">
                            <i class="bi-share"></i>
                        </button>
                    </span>

                    @if ($this->isLikeable)
                        <div class="ms-auto">
                            <a
                                @class(['btn', 'btn-sm', 'rounded-pill', 'z-1', 'btn-danger' => $this->isOrganLiked($organ), 'btn-outline-danger' => !$this->isOrganLiked($organ)])
                                @can($this->gateLikeEntity, $organ)
                                    wire:click="likeToggle({{ $organ->id }})"
                                @else
                                    href="{{ route('login') }}"
                                @endcan
                                data-bs-toggle="tooltip"
                                data-bs-title="{{ __('Přidat do oblíbených') }}"
                            >
                                <i class="bi-heart"></i> {{ $organ->likes_count }}
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif
    
</div>
