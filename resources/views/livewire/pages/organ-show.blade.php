<?php

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use App\Services\MarkdownConvertorService;
use App\Models\Disposition;
use App\Models\Organ;
use App\Models\Scopes\OwnedEntityScope;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public Organ $organ;

    protected MarkdownConvertorService $markdownConvertor;

    const
        SESSION_KEY_SHOW_MAP = 'organs.show.show-map',
        SESSION_KEY_SHOW_DISPOSITION = 'organs.show.show-disposition',
        SESSION_KEY_SHOW_LITERATURE = 'organs.show.show-literature';

    public function mount()
    {
        if (!request()->hasValidSignature(false)) {
            $this->authorize('view', $this->organ);
        }
    }

    public function boot(MarkdownConvertorService $markdownConvertor)
    {
        $this->markdownConvertor = $markdownConvertor;

        $this->organ->load(['dispositions' => function (HasMany $query) {
            if (request()->hasValidSignature(false))
                $query->withoutGlobalScope(OwnedEntityScope::class);
        }]);
    }

    public function rendering(View $view): void
    {
        $title = "{$this->organ->municipality}, {$this->organ->place}";
        $title .= ' - ' . __('varhany');
        $view->title($title);
    }

    #[Computed]
    public function categoryGroups()
    {
        $groups = [];
        foreach ($this->organ->organCustomCategories as $category) {
            $groups['custom'] ??= [];
            $groups['custom'][] = $category;
        }

        foreach ($this->organ->organCategories as $category) {
            $categoryEnum = $category->getEnum();
            $color = $categoryEnum->getColor();
            $groups[$color] ??= [];
            $groups[$color][] = $categoryEnum;
        }
        return $groups;
    }
    
    #[Computed]
    public function images()
    {
        $images = [];
        if ($this->organ->image_url) $images[] = [$this->organ->image_url, $this->organ->image_credits];
        if ($this->organ->outside_image_url) $images[] = [$this->organ->outside_image_url, $this->organ->outside_image_credits] ;
        return $images;
    }

    #[Computed]
    private function previousUrl()
    {
        $previousUrl = url()->previous();
        if ($previousUrl === route('welcome') || $previousUrl === route('organs.edit', [$this->organ->id])) {
            return route('organs.index');
        }
        return $previousUrl;
    }

    private function getDispositionUrl(Disposition $disposition)
    {
        $fn = !Gate::allows('view', $disposition) ? URL::signedRoute(...) : route(...);
        $relativeUrl = $fn('dispositions.show', $disposition->slug, absolute: false);
        return url($relativeUrl);
    }

}; ?>

<div class="organ-show container">
    <div class="d-md-flex justify-content-between align-items-center gap-4 mb-2">
        <div>
            <h3 class="lh-sm">
                <strong>{{ $organ->municipality }}</strong>
                <br />
                {{ $organ->place }}
                @if (!$organ->isPublic())
                    <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
                @endif
            </h3>

            @if (isset($organ->perex))
                <p class="lead">{{ $organ->perex }}</p>
            @endif
        </div>
        
        <div class="text-center">
            <div class="position-relative d-inline-block">
                @if ($organ->image_url)
                    <a href="{{ $organ->image_url }}" target="_blank">
                        <img class="organ-img rounded border" src="{{ $organ->image_url }}" @isset($organ->image_credits) title="{{ __('Licence obrázku') }}: {{ $organ->image_credits }}" @endisset height="200" />
                    </a>
                @endif
                <img width="100" class="region position-absolute start-0 m-2 bottom-0" src="{{ Vite::asset("resources/images/regions/{$organ->region_id}.png") }}" />
            </div>
        </div>
    </div>
    
    <table class="table mb-4">
        <tr>
            <th>{{ __('Varhanář') }}</th>
            <td>
                @if ($organ->organBuilder)
                    @php $showYearBuilt = $organ->organRebuilds->isNotEmpty(); @endphp
                    <x-organomania.organ-builder-link :organBuilder="$organ->organBuilder" :yearBuilt="$showYearBuilt ? $organ->year_built : null" />
                    @foreach ($organ->organRebuilds as $rebuild)
                        @if ($rebuild->organBuilder)
                            <br />
                            <x-organomania.organ-builder-link :organBuilder="$rebuild->organBuilder" :yearBuilt="$rebuild->year_built" :isRebuild="true" />
                        @endif
                    @endforeach
                @else
                    {{ __('neznámý') }}
                @endif
            </td>
        </tr>
        @if ($organ->year_built)
            <tr>
                <th>{{ __('Rok stavby') }}</th>
                <td>{{ $organ->year_built }}</td>
            </tr>
        @endif
        @if ($organ->renovationOrganBuilder)
            <tr>
                <th>{{ __('Rekonstrukce/restaurování') }}</th>
                <td>
                    <x-organomania.organ-builder-link :organBuilder="$organ->renovationOrganBuilder" :yearBuilt="$organ->year_renovated" />
                </td>
            </tr>
        @endif
        <tr>
            <th>{{ __('Kraj') }}</th>
            <td>{{ $organ->region->name }}</td>
        </tr>
        @if ($organ->manuals_count)
            <tr>
                <th>{{ __('Počet manuálů') }}</th>
                <td>{{ $organ->manuals_count }}</td>
            </tr>
        @endif
        @if ($organ->stops_count)
            <tr>
                <th>{{ __('Počet rejstříků') }}</th>
                <td>{{ $organ->stops_count }}</td>
            </tr>
        @endif
        <tr>
            <th>{{ __('Kategorie') }}</th>
            <td>
                @foreach ($this->categoryGroups as $group)
                    @foreach ($group as $category)
                        <x-organomania.category-badge :category="$category" />
                    @endforeach
                    @if (!$loop->last)
                        <br />
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th>{{ __('Význam') }}</th>
            <td>
                <x-organomania.stars :count="round($organ->importance / 2)" :showCount="true" />
            </td>
        </tr>
        @if (isset($organ->web))
            <tr>
                <th>{{ __('Webové odkazy') }}</th>
                <td>
                    @foreach (explode("\n", $organ->web) as $url)
                        <a class="icon-link icon-link-hover" target="_blank" href="{{ $url }}">
                            <i class="bi bi-link-45deg"></i>
                            {{ str($url)->limit(65) }}
                        </a>
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </td>
            </tr>
        @endif
        @isset($organ->varhany_net_id)
            <tr>
                <th>{{ __('Rejstřík varhan') }}</th>
                <td>
                    <a class="icon-link icon-link-hover" target="_blank" href="{{ url()->query('http://www.varhany.net/cardheader.php', ['lok' => $organ->varhany_net_id]) }}">
                        <i class="bi bi-link-45deg"></i>
                        varhany.net
                    </a>
                </td>
            </tr>
        @endisset
        @if ($organ->festivals->isNotEmpty())
            <tr>
                <th>{{ __('Významné festivaly') }}</th>
                <td>
                    @foreach ($organ->festivals as $festival)
                        <a class="link-primary text-decoration-none" wire:navigate href="{{ route('festivals.show', [$festival->id]) }}">
                            {{ $festival->name }}
                        </a>
                        @if (isset($festival->locality) || isset($festival->frequency))
                            <span class="text-body-secondary">
                                ({{ collect([$festival->locality ?? null, $festival->frequency ?? null])->filter()->join(', ') }})
                            </span>
                        @endif
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </td>
            </tr>
        @endif
        @if (isset($organ->description))
        <tr class="d-none d-md-table-row">
            <th>{{ __('Popis') }}</th>
            <td>{{ $organ->description }}</td>
        </tr>
        <tr class="d-md-none">
            <td colspan="2">
                <strong>{{ __('Popis') }}</strong>
                <br />
                {{ $organ->description }}
            </td>
        </tr>
        @endif
    </table>
    
    @if (count($this->images) > 1)
        <x-organomania.gallery-carousel :images="$this->images" class="mb-4" />
    @endif
    
    <div class="accordion">
        @if (isset($organ->disposition) || $organ->dispositions->isNotEmpty())
            <x-organomania.accordion-item
                id="accordion-disposition"
                title="{{ __('Dispozice') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_DISPOSITION)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_DISPOSITION }}')"
            >
                @if ($organ->dispositions->isNotEmpty())
                    <h5>{{ __('Interaktivní zobrazení') }}</h5>
                    @foreach ($organ->dispositions as $disposition)
                        <div>
                            <a wire:navigate class="link-primary text-decoration-none" href="{{ $this->getDispositionUrl($disposition) }}">
                                {{ $disposition->name }}
                            </a>
                            @if (!$disposition->isPublic())
                                <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
                            @endif
                        </div>
                    @endforeach
                @endif
                @isset($organ->disposition)
                    @if ($organ->dispositions->isNotEmpty())
                        <h5 class="mt-4">{{ __('Textové zobrazení') }}</h5>
                    @endif
                    <div class="markdown accordion-disposition">{!! $this->markdownConvertor->convert($organ->disposition) !!}</div>
                @endisset
            </x-organomania.accordion-item>
        @endif

        <x-organomania.accordion-item
            id="accordion-map"
            title="{{ __('Mapa') }}"
            :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_MAP)"
            onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_MAP }}')"
        >
            <x-organomania.map-detail :latitude="$organ->latitude" :longitude="$organ->longitude" />
        </x-organomania.accordion-item>

        @isset($organ->literature)
            <x-organomania.accordion-item
                id="accordion-literature"
                title="{{ __('Literatura') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_LITERATURE)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_LITERATURE }}')"
            >
                @foreach (explode("\n", $organ->literature) as $literature1)
                    <p @class(['mb-0' => $loop->last])>{{ $literature1 }}</p>
                @endforeach
            </x-organomania.accordion-item>
        @endisset
    </div>
    
    <div class="hstack mt-3">
        <a class="btn btn-sm btn-outline-primary" href="{{ route('dispositions.create', ['organId' => $organ->id]) }}">
            <i class="bi-plus-lg"></i> {{ __('Přidat dispozici') }}
        </a>
        
        <a class="btn btn-sm btn-secondary ms-auto" wire:navigate href="{{ $this->previousUrl }}"><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>&nbsp;
        @can('update', $organ)
            <a class="btn btn-sm btn-outline-primary" wire:navigate href="{{ route('organs.edit', ['organ' => $organ->id]) }}"><i class="bi-pencil"></i> {{ __('Upravit') }}</a>
        @endcan
    </div>
</div>
