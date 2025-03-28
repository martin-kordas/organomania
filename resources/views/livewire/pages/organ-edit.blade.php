<?php

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\Region;
use App\Interfaces\GeocodingService;
use App\Livewire\Forms\OrganForm;
use App\Livewire\Forms\DispositionOcrForm;
use App\Enums\OrganCategory;
use App\Repositories\OrganRepository;
use App\Services\AI\DispositionOcr;
use App\Traits\ConvertEmptyStringsToNull;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use WithFileUploads;
    use ConvertEmptyStringsToNull;

    #[Locked]
    public Organ $organ;

    public OrganForm $form;
    public DispositionOcrForm $dispositionOcrForm;

    private OrganRepository $repository;
    private GeocodingService $geocodingService;

    #[Url]
    public string $public = '0';

    public $previousUrl;

    public $dispositionOcrResult;

    public function boot(OrganRepository $repository, GeocodingService $geocodingService)
    {
        $this->repository = $repository;
        $this->geocodingService = $geocodingService;
        $this->form->boot($this->public);

        if ($this->public) {
            Gate::authorize('createPublic', Organ::class);
        }
    }

    public function rendering(View $view): void
    {
        $title = __($this->getTitle());
        $view->title($title);
    }

    public function updated($property)
    {
        if (str($property)->startsWith('form.rebuilds.')) {
            $this->form->rebuilds = collect($this->form->rebuilds)
                ->sortBy(function ($rebuild) {
                    if (!isset($rebuild['yearBuilt']) || $rebuild['yearBuilt'] === '') return INF;
                    else return $rebuild['yearBuilt'];
                })
                ->values()
                ->all();
        }
    }

    public function updatedFormWeb()
    {
        $this->form->updatedWeb();
    }

    #[Renderless]
    public function geocode()
    {
        $locationEmpty
            = ($this->form->latitude ?? 1) === 1
            && ($this->form->longitude ?? 1) === 1;

        if ($locationEmpty && $this->form->municipality && $this->form->place) {
            try {
                $address = "{$this->form->municipality} {$this->form->place}";
                $res = $this->geocodingService->geocode($address);

                $this->form->latitude = $res['latitude'];
                $this->form->longitude = $res['longitude'];
                $this->form->regionId ??= $res['regionId'];

                // akce je Renderless a hodnoty nastavujeme v JS, aby GUI nepřeblikávalo
                $js = sprintf(<<<JS
                    $('#latitude').val(%s)
                    $('#longitude').val(%s)
                    if (!$('#regionId').val()) {
                        $('#regionId').val(%s)
                        $('#regionId').trigger('change')
                    }

                    showToast("geocoded")
                JS, json_encode($res['latitude']), json_encode($res['longitude']), json_encode($res['regionId']));
                $this->js($js);
            }
            catch (Exception $ex) { }
        }
    }

    public function mount()
    {
        $this->organ ??= new Organ();
        $ability = $this->organ->exists ? 'update' : 'create';
        $this->authorize($ability, $this->organ);

        $this->form->setOrgan($this->organ);
        $this->previousUrl = request()->headers->get('referer');

        // zobrazení chybových hlášek již na začátku kvůli přehledu, které údaje jsou povinné
        if (!$this->organ->exists) $this->form->validate();
    }

    public function delete()
    {
        $this->authorize('delete', $this->organ);
        $this->form->delete();
        session()->flash('status-success', __('Varhany byly úspěšně smazány.'));

        if (
            isset($this->previousUrl)
            // nemůžeme přesměrovat na detail záznamu, který jsme smazali
            && !in_array($this->previousUrl, [
                route('organs.show', $this->organ->slug),
                route('organs.show', $this->organ->id)
            ])
        ) {
            $this->redirect($this->previousUrl, navigate: true);
        }
        else $this->redirectRoute('organs.index', navigate: true);
    }

    public function save()
    {
        $params = [];
        // při vkládání nových varhan není jasná jejich pozice v rámci seznamu varhan - přehlednější je tedy tabulkové zobrazení
        if (!$this->organ->exists) $params['viewType'] = 'table';

        try {
            $this->form->save();
        }
        catch (\Exception $ex) {
            // většina povinných údajů, kde vznikají chyby, jsou v horní části stránky
            $this->js('scrollToTop()');
            throw $ex;
        }

        $this->form->organ->refresh();
        session()->flash('status-success', __('Varhany byly úspěšně uloženy.'));
        if (isset($this->previousUrl) && $this->organ->exists) $this->redirect($this->previousUrl, navigate: true);
        else $this->redirectRoute('organs.show', ['organSlug' => $this->form->organ->slug], navigate: true);
    }

    #[Computed]
    public function regions()
    {
        return Region::query()->orderBy('name')->get();
    }

    #[Computed]
    public function organBuilders()
    {
        $query = OrganBuilder::query();
        // k nesoukromým varhanám lze uložit jen nesoukromého varhanáře
        if ($this->form->isOrganPublic()) $query->public();
        return $query->orderByName()->get();
    }

    #[Computed]
    public function organCustomCategories()
    {
        return $this->repository->getCustomCategories(withCount: []);
    }

    #[Computed]
    public function organCategoriesGroups()
    {
        return OrganCategory::getCategoryGroups();
    }

    #[Computed]
    public function organCustomCategoriesGroups()
    {
        return ['custom' => $this->organCustomCategories];
    }

    #[Computed]
    public function dispositionPlaceholder()
    {
        return match (app()->getLocale()) {
            'cs' => <<<EOL
                **I. manuál** (C-g3)
                Principál 8'
                Oktáva 4'
                Mixtura 3-4x 2 2/3'

                **Pedál**
                Subbas 16'
                Oktávbas 8'
                I/P
                EOL,

            default => <<<EOL
                **I. manual** (C-g3)
                Prinzipal 8'
                Oktave 4'
                Mixtur 3-4x 2 2/3'

                **Pedal**
                Subbas 16'
                Oktavbas 8'
                I/P
                EOL
        };
    }

    private function getCustomCategoryGroupName()
    {
        return __('Vlastní kategorie');
    }

    public function addRebuild()
    {
        $this->form->rebuilds[] = [];
    }

    public function deleteRebuild($i)
    {
        if (!isset($this->form->rebuilds[$i])) throw new \RuntimeException;

        $this->form->rebuilds = collect($this->form->rebuilds)
            ->forget($i)
            ->values()
            ->all();
    }

    public function getTitle()
    {
        if ($this->organ->exists) return 'Upravit varhany';
        elseif ($this->public) return 'Přidat varhany (veřejně)';
        else return 'Přidat varhany';
    }

    public function getStarsCount()
    {
        if (isset($this->form->importance)) return round($this->form->importance / 2);
        return null;
    }

    public function rendered()
    {
        $this->dispatch("select2-rendered");
    }

    #[Computed]
    public function uploadedOcrPhotos() {
        $useCaptions = count($this->dispositionOcrForm->photos) > 1;

        return collect($this->dispositionOcrForm->photos)->map(function ($photo) use ($useCaptions) {
            static $no = 1;

            try {
                $temporaryUrl = $photo->temporaryUrl();     // není-li soubor obrázek, vyhodí výjimku
                $caption = $useCaptions ? (__('Obrázek č.') . ' ' . $no++) : null;
                $caption = $photo->getClientOriginalName();
                return [$temporaryUrl, null, $caption];
            }
            catch (Exception $ex) {
                return null;
            }
        })->filter();
    }

    #[Computed]
    public function uploadedPhotos() {
        return collect($this->form->photos)->map(function ($photo) {
            try {
                $temporaryUrl = $photo->temporaryUrl();     // není-li soubor obrázek, vyhodí výjimku
                return [$temporaryUrl, null];
            }
            catch (Exception $ex) {
                return null;
            }
        })->filter();
    }

    public function doDispositionOcr(DispositionOcr $service)
    {
        $user = Auth::user();
        $rateLimiterKey = "disposition-ocr:{$user->id}";
        $limitHit = RateLimiter::tooManyAttempts($rateLimiterKey, maxAttempts: 15);

        if ($limitHit) $this->js('showToast("dispositionOcrCantExecute")');
        else {
            $this->dispositionOcrForm->validate();

            $photos = collect($this->dispositionOcrForm->photos)->map(
                fn ($photo) => $photo->path()
            )->toArray();
            $this->dispositionOcrResult = $service->doOcr($photos);
            RateLimiter::increment($rateLimiterKey, decaySeconds: 3600 * 12, amount: count($photos));
        }
    }

    public function resetDispositionOcr()
    {
        $this->dispositionOcrForm->reset();
        $this->dispositionOcrForm->resetValidation();
        unset($this->dispositionOcrResult);
    }

    public function resetPhotos()
    {
        $this->form->photos = [];
    }
    
}; ?>

<div class="organ-edit container">
    <form method="post" wire:submit="save" wire:keydown.ctrl.enter="save">
        {{-- mapa působí problém při malé šířce obrazovky a u nových varhan (dodatečné zobrazení mapy po vyplnění kraje), proto je v těchto případech skryta --}}
        @if ($this->form->regionId && $this->organ->exists)
            <img class="d-none d-xl-inline float-end z-1 position-relative" src="{{ Vite::asset("resources/images/regions/{$this->form->regionId}.png") }}" width="110" />
        @endif
        <h3>
            {{ __($this->getTitle()) }}
            @if (!$this->form->isOrganPublic())
                <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
            @endif
        </h3>
        
        <div class="mb-4 mt-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input class="form-control form-control-lg @error('form.municipality') is-invalid @enderror" id="municipality" wire:model.live="form.municipality" aria-describedby="municipalityFeedback" wire:blur="geocode" autofocus>
                        <label for="municipality">{{ __('Obec') }}</label>
                        @error('form.municipality')
                            <div id="municipalityFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-floating">
                        <input class="form-control form-control-lg @error('form.place') is-invalid @enderror" id="place" wire:model.live="form.place" aria-describedby="placeFeedback" wire:blur="geocode">
                        <label for="place">{{ __('Místo') }}</label>
                        @error('form.place')
                            <div id="placeFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
              
                <div class="col-md-6">
                    <label for="organBuilderId" class="form-label">{{ __('Varhanář') }}</label>&nbsp;
                    <small>(<a class="text-decoration-none" href="{{ route('organ-builders.create') }}" target="_blank">{{ __('přidat varhanáře') }}</a>)</small>
                    <x-organomania.selects.organ-builder-select
                        id="organBuilderId"
                        model="form.organBuilderId"
                        :organBuilders="$this->organBuilders"
                        :allowClear="true"
                    />
                </div>
                <div class="col-md-2">
                    <label for="yearBuilt" class="form-label">{{ __('Rok stavby') }}</label>
                    <input class="form-control" id="yearBuilt" type="number" min="0" max="3000" wire:model.number="form.yearBuilt" placeholder="1876">
                </div>
                <div class="col-md-2">
                    <label for="manualsCount" class="form-label">{{ __('Počet manuálů') }}</label>
                    <input class="form-control" id="manualsCount" type="number" min="0" max="10" wire:model.number="form.manualsCount">
                </div>
                <div class="col-md-2">
                    <label for="stopsCount" class="form-label">{{ __('Počet rejstříků') }}</label>
                    <input class="form-control" id="stopsCount" type="number" min="0" max="1000" wire:model.number="form.stopsCount">
                </div>
                
                @foreach ($this->form->rebuilds as $i => $rebuild)
                    <div class="col-md-6" wire:key="{{ "rebuildsOrganBuilder$i" }}">
                        <label for="{{ "rebuildOrganBuilderId$i" }}" class="form-label">{{ __('Přestavující varhanář') }}</label>
                        {{-- TODO: není select2, protože model se nesynchronizuje (muslo by se volat refreshSelect2() při přidání nového rebuildu) --}}
                        {{-- - např. se může vyvolat událost s parametrem s ID konkrétního selectu, na němž se má refreshSelect2() provést --}}
                        <x-organomania.selects.organ-builder-select
                            id="rebuildOrganBuilderId{{ $i }}"
                            model="form.rebuilds.{{ $i }}.organBuilderId"
                            :organBuilders="$this->organBuilders"
                            :select2="false"
                        />
                    </div>
                    <div class="col-md-2" wire:key="{{ "rebuildsYearBuilt$i" }}">
                        <label for="{{ "rebuildYearBuilt$i" }}" class="form-label">{{ __('Rok přestavby') }}</label>
                        <input class="form-control @error("form.rebuilds.$i.yearBuilt") is-invalid @enderror" id="{{ "rebuildYearBuilt$i" }}" type="number" min="0" max="3000" wire:model.live="form.rebuilds.{{ $i }}.yearBuilt">
                        @error("form.rebuilds.$i.yearBuilt")
                            <div id="{{ "rebuildYearBuiltFeedback$i" }}" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-auto" wire:key="{{ "rebuildsDelete$i" }}">
                        <label class="invisible form-label">{{ __('Smazat') }}</label><br />
                        <button type="button" class="delete-rebuild btn btn-danger btn-sm" data-rebuild-index="{{ $i }}" data-bs-toggle="modal" data-bs-target="#confirmDeleteRebuildModal">
                            <i class="bi-trash"></i> {{ __('Smazat') }}
                        </button>
                    </div>
                    <div class="w-100 mt-0" wire:key="{{ "rebuildsNewline$i" }}"></div>
                @endforeach
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm" type="button" wire:click="addRebuild"><i class="bi-plus-lg"></i> {{ __('Přidat přestavbu') }}</button>
                </div>
            </div>
            
            <div class="row g-3 bg-light rounded p-2 mt-4">
                <h5 class="mt-1 mb-0">{{ __('Kategorizace') }}</h5>
                
                <div class="col-md-6">
                    <label for="categories" class="form-label">
                        {{ __('Kategorie') }}
                        @if ($public) <span class="text-danger">*</span> @endif
                    </label>
                    <x-organomania.selects.organ-category-select
                        id="categories"
                        model="form.categories"
                        placeholder="{{ __('Zvolte kategorii varhan...') }}"
                        :categoriesGroups="$this->organCategoriesGroups"
                        :customCategoriesGroups="$this->organCustomCategoriesGroups"
                        :counts="false"
                        :live="true"
                    />
                </div>
                
                <div class="col-md-6">
                    <label for="name" class="form-label">{{ __('Význam') }} <span class="text-secondary">({{ __('od 1 do 10') }})</span> <span class="text-danger">*</span></label>
                    <div class="hstack gap-3">
                        <input class="form-control w-auto flex-grow-1 @error('form.importance') is-invalid @enderror" type="number" id="name" placeholder="4" min="1" max="10" wire:model.live.number="form.importance" aria-describedby="importanceFeedback">
                        <x-organomania.stars :count="$this->getStarsCount()" />
                        @error('form.importance')
                            <div id="importanceFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-3">
                    <label for="regionId" class="form-label @error("form.regionId") is-invalid @enderror">{{ __('Kraj') }} <span class="text-danger">*</span></label>
                    <x-organomania.selects.region-select :regions="$this->regions" id="regionId" model="form.regionId" />
                    @error('form.regionId')
                        <div id="regionIdFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="latitude" class="form-label @error("form.latitude") is-invalid @enderror">{{ __('Zeměpisná šířka') }} <span class="text-danger">*</span></label>
                    <input class="form-control" id="latitude" min="-90" max="90" type="number" step="0.0000001" wire:model="form.latitude" placeholder="40,1234567">
                    @error('form.latitude')
                        <div id="latitudeFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="longitude" class="form-label @error("form.longitude") is-invalid @enderror">{{ __('Zeměpisná délka') }} <span class="text-danger">*</span></label>
                    <input class="form-control" id="longitude" type="number" min="-180" max="180" step="0.0000001" wire:model="form.longitude" placeholder="20,1234567">
                    @error('form.longitude')
                        <div id="longitudeFeedback" class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text text-end">
                            <a class="text-decoration-none" href="#" onclick="return openMap()">{{ __('Zobrazit na mapě') }}</a>
                        </div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="visibility-hidden"></label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="concertHall" wire:model="form.concertHall" @checked($this->form->concertHall)>
                        <label class="form-check-label" for="concertHall">{{ __('Koncertní síň') }}</label>
                    </div>
                </div>
            </div>
            
            <div class="row g-3 bg-light rounded p-2 mt-4">
                <h5 class="mt-1 mb-0">{{ __('Dokumentace') }}</h5>
                
                <div>
                    <div class="d-flex align-items-end mb-2">
                        <label for="disposition" class="form-label me-auto mb-0">{{ __('Disposition_1') }}</label>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            data-bs-toggle="modal"
                            @can('useAI')
                                data-bs-target="#dispositionOcrModal"
                            @else
                                data-bs-target="#premiumModal"
                            @endcan
                        >
                            <i class="bi-magic"></i>
                            {{ __('Přečíst z fotografie') }}
                        </button>
                    </div>
                    <textarea rows="10" class="form-control" id="disposition" wire:model="form.disposition" placeholder="{{ $this->dispositionPlaceholder }}"></textarea>
                    <span class="form-text">
                        {{ __('Pro správné formátování dispozice používejte pro označení stopové výšky jednoduché uvozovky ( \' ).') }}
                        {{ __('Pro ztučnění písma nadpisů uveďte hvězdičky (**).') }}
                    </span>
                </div>
                <div>
                    <label for="description" class="form-label">{{ __('Popis') }}</label>
                    <textarea rows="8" class="form-control" id="description" wire:model="form.description" placeholder="{{ __('Podrobnější popis varhan, který se vypíše v detailním zobrazení.') }}"></textarea>
                </div>
                <div>
                    <label for="perex" class="form-label">{{ __('Perex') }}</label>
                    <textarea rows="3" class="form-control" id="perex" wire:model="form.perex" placeholder="{{ __('Krátká jednovětá charakteristika varhan, která se vypíše v rámečku s miniaturou.') }}"></textarea>
                </div>
            </div>
            
            <div class="row g-3 bg-light rounded p-2 mt-4">
                <h5 class="mt-1 mb-0">{{ __('Externí materiály') }}</h5>
                
                {{-- TODO: soubory lze jen přidávat, ne mazat --}}
                <div>
                    <label for="photos" class="form-label">{{ __('Přidat obrázky') }}</label>
                    <small class="text-body-secondary" wire:loading wire:target="form.photos">({{ __('nahrávání souboru') }}&hellip;)</small>
                    @php $isFileError = $errors->has('form.photos') || $errors->has('form.photos.*'); @endphp
                    @if (!empty($this->form->photos) && $this->uploadedPhotos->isNotEmpty() && !$isFileError)
                        <x-organomania.gallery-carousel :images="$this->uploadedPhotos" class="mb-2" :noAdditional="true" />
                        <div class="text-end">
                            <button class="btn btn-sm btn-outline-secondary" type="button" wire:click="resetPhotos">
                                <i class="bi bi-x-lg"></i> {{ __('Nahrát jiné fotografie') }}
                            </button>
                        </div>
                    @else
                        <input id="photos" class="form-control @if ($isFileError) is-invalid @endif" type="file" wire:model="form.photos" aria-describedby="photosFeedback" multiple>
                        @if ($isFileError)
                            @error('form.photos')
                                <div id="photosFeedback" class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('form.photos.*')
                                <div id="photosFeedback" class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                        <div class="form-text">
                            {{ __('Můžete zvolit 1 až 10 fotografií velkých maximálně 4 MB.') }}
                        </div>
                    @endif
                </div>
                
                <div>
                    <label for="recordings" class="form-label">{{ __('Přidat nahrávky') }}</label>
                    <small class="text-body-secondary" wire:loading wire:target="form.recordings">({{ __('nahrávání souboru') }}&hellip;)</small>
                    @php $isFileError = $errors->has('form.recordings') || $errors->has('form.recordings.*'); @endphp
                    <input id="recordings" class="form-control @if ($isFileError) is-invalid @endif" type="file" wire:model="form.recordings" aria-describedby="recordingsFeedback" multiple>
                    @if ($isFileError)
                        @error('form.recordings')
                            <div id="recordingsFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('form.recordings.*')
                            <div id="recordingsFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    @endif
                    <div class="form-text">
                        {{ __('Můžete zvolit 1 až 5 nahrávek velkých maximálně 10 MB.') }}
                    </div>
                </div>
                
                <div>
                    <label for="imageUrl" class="form-label">{{ __('URL obrázku') }} &ndash; {{ __('pro miniaturu') }}</label>
                    <input class="form-control @error('form.imageUrl') is-invalid @enderror" id="imageUrl" wire:model="form.imageUrl" aria-describedby="imageUrlFeedback">
                    
                    @error('form.imageUrl')
                        <div id="imageUrlFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        {{ __('Obrázek můžete nahrát např. přes službu') }} <a href="https://postimages.org/" target="_blank">postimages.org</a> {{ __('a sem zkopírovat vygenerovaný "přímý odkaz"') }}.
                    </div>
                </div>
                <div>
                    <label for="imageCredits" class="form-label">{{ __('Licence obrázku') }} &ndash; {{ __('pro miniaturu') }}</label>
                    <input class="form-control @error('form.imageCredits') is-invalid @enderror" id="imageCredits" wire:model="form.imageCredits" aria-describedby="imageCreditsFeedback">
                    @error('form.imageCredits')
                        <div id="imageCreditsFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                {{-- externí obrázek uživatelům pro zkrácení stránky nezobrazujeme - mohou nahrávat pomocí photos --}}
                @if ($this->form->isOrganPublic())
                    <div>
                        <label for="outsideImageUrl" class="form-label">
                            {{ __('URL obrázku') }} &ndash; {{ __('exteriér') }}
                        </label>
                        <input class="form-control @error('form.outsideImageUrl') is-invalid @enderror" id="outsideImageUrl" wire:model="form.outsideImageUrl" aria-describedby="outsideImageUrlFeedback">
                        @error('form.outsideImageUrl')
                            <div id="outsideImageUrlFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('Obrázek můžete nahrát např. přes službu') }} <a href="https://postimages.org/" target="_blank">postimages.org</a> {{ __('a sem zkopírovat vygenerovaný "přímý odkaz"') }}.
                        </div>
                    </div>
                    <div>
                        <label for="outsideImageCredits" class="form-label">
                            {{ __('Licence obrázku') }} &ndash; {{ __('exteriér') }}
                        </label>
                        <input class="form-control @error('form.outsideImageCredits') is-invalid @enderror" id="outsideImageCredits" wire:model="form.outsideImageCredits" aria-describedby="outsideImageCreditsFeedback">
                        @error('form.outsideImageCredits')
                            <div id="outsideImageCreditsFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
                <div>
                    <label for="web" class="form-label">{{ __('Web') }}</label>
                    <textarea rows="2" class="form-control @error('form.webArray.*') is-invalid @enderror" id="web" wire:model="form.web" aria-describedby="webFeedback"></textarea>
                    @error('form.webArray.*')
                        <div id="webFeedback" class="invalid-feedback">{{ $errors->first('form.webArray.*') }}</div>
                    @enderror
                    <div class="form-text">
                        {{ __('Více webových odkazů zadejte na samostatných řádcích.') }}
                    </div>
                </div>
                <div>
                    <label for="literature" class="form-label">{{ __('Literatura') }}</label>
                    <textarea rows="3" class="form-control" id="literature" wire:model="form.literature"></textarea>
                    <div class="form-text">
                        {{ __('Každá publikace se uvede na samostatném řádku.') }}
                    </div>
                </div>
            </div>
        </div>
    
        <div class="hstack">
            @if ($this->organ->exists)
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="bi-trash"></i> Smazat</button>
            @endif
                
            <small class="text-secondary ms-auto ps-2 me-2"><i class="bi-info-circle-fill"></i> {!! __('Stiskněte <kbd>Ctrl+Enter</kbd> pro uložení') !!}</small>
            <a class="btn btn-sm btn-secondary" href="{{ url()->previous() }}"><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>&nbsp;
            <button type="submit" class="btn btn-sm btn-primary">
                <span wire:loading.remove wire:target="save">
                    <i class="bi-floppy"></i> {{ __('Uložit') }}
                </span>
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span class="visually-hidden" role="status">{{ __('Načítání...') }}</span>
                </span>
            </button>
        </div>
    </form>
    
    <x-organomania.modals.confirm-modal
        id="confirmDeleteModal"
        title="{{ __('Smazat') }}"
        buttonLabel="{{ __('Smazat') }}"
        buttonColor="danger"
        onclick="$wire.delete()"
    >
        {{ __('Opravdu chcete varhany smazat?') }}
    </x-organomania.modals.confirm-modal>
    
    <x-organomania.modals.confirm-modal
        id="confirmDeleteRebuildModal"
        title="{{ __('Smazat') }}"
        buttonLabel="{{ __('Smazat') }}"
        buttonColor="danger"
        onclick="deleteRebuild()"
    >
        {{ __('Opravdu chcete smazat přestavbu?') }}
    </x-organomania.modals.confirm-modal>
    
    <x-organomania.toast toastId="geocoded">
        {{ __('Souřadnice místa varhan byly automaticky vypočítány.') }}
    </x-organomania.toast>
    <x-organomania.toast toastId="dispositionOcrCantExecute" color="danger">
        {{ __('Přečtení fotografií nelze provést, protože byl dosažen limit zpracování 15 fotografií denně.') }}
    </x-organomania.toast>
    <x-organomania.toast toastId="locationIncomplete" color="danger">
        {{ __('Souřadnice nejsou úplné.') }}
    </x-organomania.toast>
  
    <x-organomania.modals.premium-modal />
    <x-organomania.modals.disposition-ocr-modal />
</div>

@script
<script>
    window.deleteRebuild = function () {
        var rebuildIndex = confirmModal.getInvokeButton('confirmDeleteRebuildModal').dataset.rebuildIndex
        $wire.deleteRebuild(rebuildIndex)
    }
        
    window.openMap = function () {
        let lat = $('#latitude').val()
        let lon = $('#longitude').val()
        if (!lat | !lon) showToast('locationIncomplete')
        else {
            let url = getMapUrl(lat, lon)
            window.open(url, '_blank')
        }
        return false
    }
</script>
@endscript
