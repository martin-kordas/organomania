@props(['record'])

<div class="btn-group">
    <a class="btn btn-sm btn-primary text-nowrap" data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit') }}" href="{{ $this->getViewUrl($record) }}" wire:navigate>
        <i class="bi-eye"></i>
    </a>

    @can('update', $record)
        <a class="btn btn-sm btn-outline-primary text-nowrap" data-bs-toggle="tooltip" data-bs-title="{{ __('Upravit') }}" href="{{ route($this->editRoute, $record->id) }}" wire:navigate>
            <i class="bi-pencil"></i>
        </a>
    @endcan

    <button class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="visually-hidden">{{ __('Podrobnosti') }}</span>
    </button>

    <ul class="dropdown-menu">
        @can([$this->gateUseCustomCategories, 'view'], $record)
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#customCategoriesModal" wire:click="setEditCustomCategoriesOrgan({{ $record->id }})">
                    <i class="bi-tag"></i> {{ __('Vlastní kategorie') }}
                    @if ($record->{$this->customCategoriesCountProp} > 0)
                        <span class="badge text-bg-info rounded-pill">{{ $record->{$this->customCategoriesCountProp} }}</span>
                    @endif
                </a>
            </li>
        @endcan

        <li>
            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $this->getShareUrl($record) }}">
                <i class="bi-share"></i> {{ __('Sdílet') }}
            </a>
        </li>

        @if ($this->isLikeable)
            @can($this->gateLikeEntity, $record)
                <li>
                    <a @class(['dropdown-item', 'text-bg-danger' => $this->isOrganLiked($record)]) href="#" wire:click="likeToggle({{ $record->id }})">
                        <i class="bi-heart"></i>
                        @if ($this->isOrganLiked($record))
                            {{ __('Odebrat z oblíbených') }}
                        @else
                            {{ __('Přidat do oblíbených') }}
                        @endif
                    </a>
                </li>
            @endcan
        @endif
    </ul>
</div>
