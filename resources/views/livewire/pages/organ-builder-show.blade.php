<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Helpers;
use App\Http\Controllers\ThumbnailController;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganBuilderTimelineItem;
use App\Models\OrganRebuild;
use App\Enums\OrganBuilderCategory;
use App\Enums\Region;
use App\Repositories\OrganBuilderRepository;
use App\Services\MarkdownConvertorService;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public OrganBuilder $organBuilder;

    protected MarkdownConvertorService $markdownConvertor;

    protected OrganBuilderRepository $repository;

    private $showActivePeriodInHeading;

    const
        SESSION_KEY_SHOW_MAP = 'organ-builders.show.show-map',
        SESSION_KEY_SHOW_LITERATURE = 'organs.show.show-literature',
        SESSION_KEY_SHOW_FAMILY_TREE = 'organs.show.show-family-tree';

    public function boot(MarkdownConvertorService $markdownConvertor, OrganBuilderRepository $repository)
    {
        $this->markdownConvertor = $markdownConvertor;
        $this->repository = $repository;

        $this->showActivePeriodInHeading
            = isset($this->organBuilder->active_period)
            && !$this->organBuilder->is_workshop
            && !in_array($this->organBuilder->active_period, ['současnost', '–']);

        $this->organBuilder->load([
            'organs' => function (HasMany $query) {
                $query->withCount('organRebuilds');
            },
            'organs.timelineItem',
        ]);
    }

    public function mount()
    {
        if (!request()->hasValidSignature(false)) {
            $this->authorize('view', $this->organBuilder);
        }
        $this->organBuilder->viewed();
    }

    public function rendering(View $view): void
    {
        $view->title($this->title);
    }

    #[Computed]
    public function title()
    {
        $title = '';
        if ($this->organBuilder->baroque) $title .= 'Barokní varhanářství na Moravě - ';
        $title .= $this->organBuilder->name;
        // alternativy: varhanářská výroba, výroba varhan
        $type = __($this->organBuilder->is_workshop ? 'varhanářství' : 'varhanář');
        $title .= " - $type";
        return $title;
    }

    #[Computed]
    public function categoryGroups()
    {
        $groups = [];
        foreach ($this->organBuilder->organBuilderCategories as $category) {
            $categoryEnum = $category->getEnum();
            $color = $categoryEnum->getColor();
            $groups[$color] ??= [];
            $groups[$color][] = $categoryEnum;
        }
        return $groups;
    }

    #[Computed]
    private function previousUrl()
    {
        $previousUrl = url()->previous();
        if ($previousUrl === route('welcome') || $previousUrl === route('organ-builders.edit', [$this->organBuilder->id])) {
            return route('organ-builders.index');
        }
        return $previousUrl;
    }

    #[Computed]
    private function descriptionHtml()
    {
        $description = $this->markdownConvertor->convert($this->organBuilder->description);
        return trim($description);
    }

    #[Computed]
    private function organBuilderCategoriesGroups()
    {
        return OrganBuilderCategory::getCategoryGroups();
    }

    #[Computed]
    private function shouldShowFamilyTree()
    {
        return !empty($this->familyTreeItems);
    }

    private function getTimelineIds($idColumn = 'id', $timelineItems = null)
    {
        $timelineItems ??= $this->organBuilder->timelineItems;
        return $timelineItems->pluck($idColumn)->filter()->unique();
    }

    #[Computed]
    private function familyTreeItems()
    {
        if ($this->organBuilder->id === OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED) return [];

        $ids = $this->getTimelineIds();
        $parentIds = $this->getTimelineIds('parent_timeline_item_id');
        $trainedByIds = $this->getTimelineIds('trained_by_timeline_item_id');

        $missingParentIds = $parentIds->diff($ids);
        $missingTrainedByIds = $trainedByIds->diff($ids);
        $missingIds = $missingParentIds->merge($missingTrainedByIds)->unique();
        $timelineItemsOutOfScope = OrganBuilderTimelineItem::query()
            ->with('organBuilder')
            ->whereIn('id', $missingIds)
            ->orWhereIn('parent_timeline_item_id', $ids)
            ->orWhereIn('trained_by_timeline_item_id', $ids)
            ->get();

        $timelineItems = $this->organBuilder->timelineItems->merge($timelineItemsOutOfScope);
        $parentIdsAll = $this->getTimelineIds('parent_timeline_item_id', $timelineItems);
        $trainedByIdsAll = $this->getTimelineIds('trained_by_timeline_item_id', $timelineItems);

        $items = [];
        foreach ($timelineItems as $timelineItem) {
            $orphan =
                !isset($timelineItem->parent_timeline_item_id)
                && !isset($timelineItem->trained_by_timeline_item_id)
                && !$parentIdsAll->contains($timelineItem->id)
                && !$trainedByIdsAll->contains($timelineItem->id);

            if (!$orphan) {
                $items[] = $this->getFamilyTreeItem($timelineItem);
            }
        }
        return $items;
    }

    private function getFamilyTreeItem(OrganBuilderTimelineItem $timelineItem)
    {
        $outOfScope = $timelineItem->organ_builder_id !== $this->organBuilder->id;
        if ($outOfScope && $timelineItem->organ_builder_id !== OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED) {
            $url = route('organ-builders.show', [$timelineItem->organBuilder->slug]);
        }
        else $url = null;

        $parts = explode(', ', $timelineItem->name, 2);
        $label = "*{$parts[0]}*";
        if (isset($parts[1])) $label .= "\n*{$parts[1]}*";
        if (isset($timelineItem->activePeriod)) $label .= "\n({$timelineItem->activePeriod})";
        $municipality = str_replace('Praha-Žižkov', 'Praha', $this->organBuilder->municipality);
        if (!$outOfScope && $timelineItem->locality !== $municipality) $label .= "\n{$timelineItem->locality}";
        $sibling = in_array($timelineItem->id, [10, 15, 861, 862, 863, 868, 29, 915, 917, 921]);
        if ($sibling) {
            $siblingLabel = match ($timelineItem->id) {
                29 => 'Neznámý vztah',
                915 => 'Strýc/synovec',
                default => 'Sourozenci',
            };
        }
        else $siblingLabel = null;

        return [
            'id' => $timelineItem->id,
            'label' => $label,
            'y' => $timelineItem->year_from * 2.5,
            'hideInTimeline' => $timelineItem->hide_in_timeline || $outOfScope,
            'parentId' => $timelineItem->parent_timeline_item_id,
            'trainedById' => $timelineItem->trained_by_timeline_item_id,
            'outOfScope' => $outOfScope,
            'url' => $url,
            'sibling' => $sibling,
            'siblingLabel' => $siblingLabel,
        ];
    }

    #[Computed]
    private function familyTreeHeight()
    {
        $count = count($this->familyTreeItems);
        return match (true) {
            $count >= 6 => 400,
            $count >= 4 => 350,
            default => 300,
        };
    }

    #[Computed]
    private function municipalityCountry()
    {
        $matches = [];
        if (preg_match('/\((.+)\)/', $this->organBuilder->municipality, $matches)) {
            $country = $matches[1];
            $municipality = trim(preg_replace('/\(.+\)/', '', $this->organBuilder->municipality));
        }
        else {
            $country = null;
            $municipality = $this->organBuilder->municipality;
        }
        return [$municipality, $country];
    }

    #[Computed]
    private function workshopMembers()
    {
        // TODO: mohli by se asi načítat přímo z organ_builder_timeline_items
        $members = [];
        if (isset($this->organBuilder->workshop_members)) {
            $workshopMembers = Helpers::normalizeLineBreaks($this->organBuilder->workshop_members);
            foreach (explode("\n", $workshopMembers) as $member) {
                $matches = [];
                if (preg_match('/^(.*?)(\(.*?\))$/u', $member, $matches)) {
                    $memberArr = [trim($matches[1]), $matches[2]];
                }
                else $memberArr = [$member, null];

                $highlighted = $memberArr[0] === request()->query('highlightWorkshopMember');
                $memberArr[] = $highlighted;

                $members[] = $memberArr;
            }
        }
        return $members;
    }

    #[Computed]
    public function metaDescription()
    {
        if (app()->getLocale() === 'cs') {
            if (isset($this->organBuilder->perex)) return $this->organBuilder->perex;
            if (isset($this->organBuilder->description)) {
                $description = $this->markdownConvertor->stripMarkDown($this->organBuilder->description);
                return str($description)->limit(200);
            }
        }
    }

    #[Computed]
    public function images()
    {
        $images = [];

        // 1) varhany postavené varhanářem
        foreach ($this->organBuilder->organs as $organ) {
            if (isset($organ->image_url, $organ->outside_image_url)) {
                $details = [];
                if (isset($organ->year_built)) $details[] = $organ->year_built;
                if (isset($organ->case_organ_builder_id) || isset($organ->case_organ_builder_name)) {
                    $details[] = __('skříň starší');
                    $shownInCases = false;
                }
                else $shownInCases = $organ->isPublic() && isset($organ->year_built) && !in_array($organ->id, Organ::ORGANS_NOT_SHOWN_IN_CASES);
                $year = !empty($details) ? implode(', ', $details) : null;

                $caption = view('components.organomania.organ-link', [
                    'organ' => $organ,
                    'showOrganBuilderExactOnly' => true,
                    'showSizeInfo' => true,
                    'showSizeInfoOriginal' => true,
                    'showShortPlace' => true,
                    'iconLink' => false,
                    'year' => $year,
                ])->render();
                $images[] = [$organ->image_url, $organ->image_credits, $caption, false, $shownInCases];
            }
        }
        $organIds = $this->organBuilder->organs->pluck('id');

        // 2) varhany opravené varhanářem
        foreach ($this->organBuilder->renovatedOrgans as $organ) {
            if (isset($organ->image_url, $organ->outside_image_url) && !$organIds->contains($organ->id)) {
                $year = __('opraveno');
                if (isset($organ->year_renovated)) $year .= " {$organ->year_renovated}";
                $caption = view('components.organomania.organ-link', [
                    'organ' => $organ,
                    'showSizeInfo' => true,
                    'showShortPlace' => true,
                    'iconLink' => false,
                    'year' => $year,
                    'isRenovation'=> true,
                ])->render();
                $images[] = [$organ->image_url, $organ->image_credits, $caption];
            }
        }
        $organIds = $organIds->merge($this->organBuilder->renovatedOrgans->pluck('id'));

        // 3) varhany postavené varhanářem, z nichž se dochovala jen skříň
        foreach ($this->organBuilder->caseOrgans as $organ) {
            if (isset($organ->image_url, $organ->outside_image_url) && !$organIds->contains($organ->id)) {
                $details = [];
                if (isset($organ->case_year_built)) {
                    $details[] = $organ->case_year_built;
                    $shownInCases = $organ->isPublic();
                }
                else $shownInCases = false;
                $details[] = __('dochována skříň');
                $year = implode(", ", $details);

                $caption = view('components.organomania.organ-link', [
                    'organ' => $organ,
                    'showCaseOrganBuilderExactOnly' => true,
                    'iconLink' => false,
                    'year' => $year,
                    'showShortPlace' => true,
                    'showDescription' => false,   // jde už o úplně jiné varhany, akduální varhanář postavil jen skříň
                ])->render();
                $images[] = [$organ->image_url, $organ->image_credits, $caption, false, $shownInCases];
            }
        }

        // 4) varhany varhanáře v dodatečných obrázcích
        foreach ($this->organBuilder->additionalImages as $additionalImage) {
            $content = '<i class="bi-music-note-list"></i> ';
            $content .= e($additionalImage->name);

            $details = [];
            if (isset($additionalImage->organ_builder_name)) $details[] = $additionalImage->organ_builder_name;
            if (isset($additionalImage->year_built)) $details[] = $additionalImage->year_built;
            if (isset($additionalImage->details)) $details[] = $additionalImage->details;
            if (!empty($details)) {
                $detailsStr = implode(', ', $details);
                $content .= sprintf(" <span class='text-body-secondary'>(%s)</span>", e($detailsStr));
            }
            $shownInCases = !$additionalImage->nonoriginal_case && isset($additionalImage->year_built);

            $images[] = [$additionalImage->image_url, $additionalImage->image_credits, $content, true, $shownInCases];
        }

        usort($images, $this->compareImages(...));

        return $images;
    }

    #[Computed]
    public function imagesShownInCases()
    {
        return array_filter(
            $this->images,
            fn ($image) => ($image[4] ?? false) === true
        );
    }

    #[Computed]
    public function mapOtherMarkers()
    {
        return collect()
            // nejméně významné body jako první (aby byly v pozadí)
            ->merge($this->organBuilder->additionalImagesWithLocation)
            ->merge($this->organBuilder->renovatedOrgans)
            ->merge($this->organBuilder->caseOrgans)
            ->merge($this->organs->pluck('organ'))
            ->unique('id');
    }

    private function compareImages(array $image1, array $image2)
    {
        $year1 = $this->getYearFromImageCaption($image1[2]) ?? 0;
        $year2 = $this->getYearFromImageCaption($image2[2]) ?? 0;
        return $year1 <=> $year2;
    }

    private function getYearFromImageCaption($caption)
    {
        $matches = [];
        if (preg_match('/[0-9]{4}/', $caption, $matches)) return $matches[0];
        return null;
    }

    #[Computed]
    private function organs()
    {
        // ::collect(): konverze Eloquent kolekce na standardní kolekci
        $organs = $this->organBuilder->organs->map(
            fn (Organ $organ) => ['isRebuild' => false, 'organ' => $organ, 'year' => $organ->year_built]
        )->collect();

        $rebuiltOrgans = $this->organBuilder->organRebuilds->filter(
            // přestavované varhany mohou být cizího uživatele, pak se vůbec nenačtou
            fn (OrganRebuild $rebuild) => isset($rebuild->organ)
        )->map(
            fn (OrganRebuild $rebuild) => ['isRebuild' => true, 'organ' => $rebuild->organ, 'year' => $rebuild->year_built]
        )->collect();

        // jsou-li v $rebuiltOrgans zahrnuty stejné varhany jako v $organs, pak $this->organs->count() je větší je počet reálně vyfiltrovaných varhan
        return $organs
            ->merge($rebuiltOrgans)
            ->sortBy('year');
    }

    #[Computed]
    private function relatedOrganBuilders()
    {
        $relatedOrganBuilderIds = match ($this->organBuilder->id) {
            // Rieger
            59 => [1, 2],
            1 => [59, 2],
            2 => [59, 1, 106],
            // Burkhardt
            28 => [8],
            // Richter
            60 => [4],
            // Silberbauer
            69 => [29],
            // Stark
            8 => [28],
            // Neusser
            50 => [72],
            // Prediger
            55 => [76, 5],
            // Eisenhut
            33 => [77],
            // Hubička
            40 => [67],
            // Paštikové
            53 => [67],
            // Organa
            52 => [7, 47],
            // Jozefy
            42 => [38],
            38 => [42],
            // Schwarz
            68 => [4],
            // Harbich
            37 => [72],
            // Výmola
            3 => [60],
            default => []
        };
        return collect($relatedOrganBuilderIds)->map(
            fn ($organBuilderId) => OrganBuilder::find($organBuilderId)
        );
    }

    #[Computed]
    private function navigationItems()
    {
        $items = ['info' => __('Základní údaje')];
        if (isset($this->organBuilder->description)) $items['description'] = __('Popis');
        if (count($this->images) > 1) $items['images'] = __('Galerie');
        if ($this->shouldShowFamilyTree) $items['accordion-family-tree-container'] = __('Rodokmen');
        if ($this->shouldShowMap) $items['accordion-map-container'] = __('Mapa');
        if (isset($this->organBuilder->literature)) {
            $literatureCount = count(explode("\n", $this->organBuilder->literature));
            $items['accordion-literature-container'] = __('Literatura') . " ($literatureCount)";
        }

        if (count($items) <= 1) return [];
        return $items;
    }

    #[Computed]
    private function shouldShowMap()
    {
        return $this->organBuilder->hasLocality();
    }

    private function shouldShowPlaceMap($place)
    {
        return isset($place) && !in_array($place, [
            'Praha', 'Brno', 'Plzeň'
        ]);
    }

}; ?>

<div class="organ-builder-show container">
    @push('meta')
        @isset($this->metaDescription)
            <meta name="description" content="{{ $this->metaDescription }}">
        @endisset

        <meta property="og:title" content="{{ $this->title }}">
        @isset($this->metaDescription)
            <meta property="og:description" content="{{ $this->metaDescription }}">
        @endisset
        @isset($organBuilder->image_url)
            <meta property="og:image" content="{{ url($organBuilder->image_url) }}">
        @endisset
    @endpush

    @if (!empty($this->navigationItems))
        <x-organomania.show-navigation-items :navigationItems="$this->navigationItems" />
    @endif

    <div class="d-md-flex justify-content-between align-items-center gap-4 mb-2">
        <div>
            <h3 class="fs-2" @if (Auth::user()?->admin) title="ID: {{ $organBuilder->id }}" @endif>
                {{ $organBuilder->name }}
                @if ($this->showActivePeriodInHeading)
                    <span class="text-body-tertiary">({{ $organBuilder->active_period }})</span>
                @endif
                @if (!$organBuilder->isPublic())
                    <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
                @endif

                <br />
                <small style="font-size: 60%">
                    {{ $this->municipalityCountry[0] }}
                    @isset ($this->municipalityCountry[1])
                        <span class="text-secondary">({{ $this->municipalityCountry[1] }})</span>
                    @endisset
                    @if ($organBuilder->region && $organBuilder->region->id !== Region::Praha->value)
                        <span class="text-secondary">
                            &nbsp;| &nbsp;<span style="font-size: var(--bs-body-font-size);">{{ $organBuilder->region->name }}</span>
                        </span>
                    @endif
                </small>
            </h3>

            @if (isset($organBuilder->perex))
                <p class="lead">{{ $organBuilder->perex }}</p>
            @endif
        </div>

         @if ($organBuilder->image_url || $organBuilder->region)
            <div class="text-center">
                <div class="position-relative d-inline-block">
                    @if ($organBuilder->image_url)
                        <a href="{{ Helpers::getImageLinkUrl($organBuilder->image_url) }}" target="_blank">
                            <img class="organ-img rounded border" src="{{ ThumbnailController::getThumbnailUrl($organBuilder->image_url) }}" @isset($organBuilder->image_credits) title="{{ __('Licence obrázku') }}: {{ $organBuilder->image_credits }}" @endisset />
                        </a>
                    @endif
                    @if ($organBuilder->region)
                        <img width="100" @class(['region', 'start-0', 'm-2', 'bottom-0', 'position-absolute' => isset($organBuilder->image_url)]) src="{{ Vite::asset("resources/images/regions/{$organBuilder->region_id}.png") }}" />
                    @endif
                </div>
            </div>
        @endif
    </div>

    @if ($organBuilder->isPublic() && $organBuilder->isInland())
        <div class="text-center mt-3">
            <x-organomania.info-alert class="d-inline-block mb-1">
                {!! __('O stylovém vývoji našeho varhanářství více') !!}
                <a class="link-primary text-decoration-none" href="{{ route('about-organ') }}" wire:navigate>{{ __('zde') }}</a>.
            </x-organomania.info-alert>
        </div>
    @endif

    <table id="info" class="table show-table mt-3 mb-2">
        @if (isset($organBuilder->place_of_birth))
            <tr>
                <th>{{ __('Místo narození') }}</th>
                <td>
                    {{ $organBuilder->place_of_birth }}
                    @if ($this->shouldShowPlaceMap($organBuilder->place_of_birth))
                        <span class="text-secondary ms-2 small">
                            <a class="icon-link icon-link-hover" href="{{ Helpers::getMapUrlPlace($organBuilder->place_of_birth) }}" target="_blank">
                                <i class="bi bi-box-arrow-up-right"></i>
                                {{ __("Mapy.cz") }}
                            </a>
                        </span>
                    @endif
                </td>
            </tr>
        @endif
        @if (isset($organBuilder->place_of_death))
        <tr>
            <th>{{ __('Místo úmrtí') }}</th>
            <td>
                {{ $organBuilder->place_of_death }}
                @if ($this->shouldShowPlaceMap($organBuilder->place_of_death))
                    <span class="text-secondary ms-2 small">
                        <a class="icon-link icon-link-hover" href="{{ Helpers::getMapUrlPlace($organBuilder->place_of_death) }}" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i>
                            {{ __("Mapy.cz") }}
                        </a>
                    </span>
                @endif
            </td>
        </tr>
        @endif
        @if (isset($organBuilder->active_period) && !$this->showActivePeriodInHeading)
        <tr>
            <th>{{ __('Období') }}</th>
            <td>{{ $organBuilder->active_period }}</td>
        </tr>
        @endif
        @php $nonCustomCategoryIds = $organBuilder->organBuilderCategories->pluck('id') @endphp
        @if ($nonCustomCategoryIds->isNotEmpty() || !empty($this->categoryGroups))
            <tr>
                <th>
                    @if ($nonCustomCategoryIds->isNotEmpty())
                        <span data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit přehled kategorií') }}" onclick="setTimeout(removeTooltips);">
                            <a class="btn btn-sm p-1 py-0 text-primary" data-bs-toggle="modal" data-bs-target="#categoriesModal" @click="highlightCategoriesInModal(@json($nonCustomCategoryIds))">
                                <i class="bi bi-question-circle"></i>
                            </a>
                        </span>
                    @endif
                    {{ __('Kategorie') }}
                </th>
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
        @endif
        @if (!$organBuilder->shouldHideImportance())
        <tr>
            <th>
                <a class="btn btn-sm p-1 py-0 text-primary" data-bs-toggle="modal" data-bs-target="#importanceHintModal">
                    <i class="bi bi-question-circle"></i>
                </a>
                {{ __('Význam') }}
            </th>
            <td>
                <x-organomania.stars :count="round($organBuilder->importance / 2)" :showCount="true" />
            </td>
        </tr>
        @endif
        @isset($organBuilder->varhany_net_id)
            <tr>
                <th>
                    {{ __('Katalog') }}
                    <span class="d-none d-md-inline">{{ __('varhanářů') }}</span>
                </th>
                <td>
                    <a class="icon-link icon-link-hover" target="_blank" href="{{ url()->query('http://www.varhany.net/zivotopis.php', ['idv' => $organBuilder->varhany_net_id]) }}">
                        <i class="bi bi-link-45deg"></i>
                        varhany.net
                    </a>
                </td>
            </tr>
        @endisset
        @if (!empty($this->workshopMembers))
        <tr>
            <th>
                @if ($this->organBuilder->is_workshop)
                    {{ __('Členové dílny') }}
                @else
                    {{ __('Členové rodu') }}
                @endif
            </th>
            <td>
                <table class="mb-0">
                    @foreach ($this->workshopMembers as [$name, $activePeriod, $highlighted])
                        <tr @style(['background: #fff3cd;' => $highlighted])>
                            <td class="pe-2">{{ $name }}</td>
                            <td class="text-body-secondary">{{ $activePeriod }}</td>
                        </tr>
                    @endforeach
                </table>
                @if (isset($organBuilder->region_id) && $organBuilder->timelineItems->count() > 0)
                    <a class="btn btn-sm btn-outline-secondary mt-1" href="{{ route('organ-builders.index', ['filterId' => $organBuilder->id, 'viewType' => 'timeline']) }}" wire:navigate>
                        <i class="bi bi-clock"></i>
                        {{ __('Časová osa') }}
                    </a>
                @endif
                @if ($this->shouldShowFamilyTree)
                    <button class="btn btn-sm btn-outline-secondary mt-1" onclick="scrollToElement('#accordion-family-tree-container', 75)">
                        {{ __('Rodokmen') }}
                    </button>
                @endif
            </td>
        </tr>
        @endif
        @if (isset($organBuilder->web))
            <x-organomania.tr-responsive title="{{ __('Webové odkazy') }}">
                <div class="text-break items-list">
                    @foreach (explode("\n", $organBuilder->web) as $url)
                        <x-organomania.web-link :url="$url" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
            </x-organomania.tr-responsive>
        @endif
        @if ($this->organs->isNotEmpty())
            <x-organomania.tr-responsive title="{{ __('Významné varhany') }}">
                <div class="text-break items-list" style="max-height: 350px; overflow-y: auto">
                    @foreach ($this->organs as ['isRebuild' => $isRebuild, 'organ' => $organ, 'year' => $year])
                            <x-organomania.organ-link :organ="$organ" :isRebuild="$isRebuild" :year="$year" :showSizeInfo="true" :showShortPlace="true" :showOrganBuilderExactOnly="true" />
                            @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
                @if ($this->organs->count() > 1)
                    <a class="btn btn-sm btn-outline-secondary mt-1 me-1" href="{{ route('organs.index', ['filterOrganBuilderId' => $organBuilder->id]) }}">
                        <i class="bi bi-music-note-list"></i>
                        {{ __('Zobrazit vše') }}
                        <span class="badge text-bg-secondary rounded-pill">{{ $this->organs->count() }}</span>
                    </a>
                    <br class="d-sm-none" />
                @endif
                @if (isset($organBuilder->region_id) && $organBuilder->timelineItems->isNotEmpty())
                    <a class="btn btn-sm btn-outline-secondary mt-1" href="{{ route('organ-builders.index', ['filterId' => $organBuilder->id, 'viewType' => 'timeline']) }}" wire:navigate>
                        <i class="bi bi-clock"></i> {{ __('Časová osa') }}
                    </a>
                @endif
                @if ($this->organs->count() > 1)
                    <a class="btn btn-sm btn-outline-secondary mt-1" href="{{ route('organs.index', ['filterOrganBuilderId' => $organBuilder->id, 'viewType' => 'chart', 'sortColumn' => 'stops_count']) }}" wire:navigate>
                        <i class="bi bi-bar-chart-line"></i>
                        {{ __('Srovnat velikost') }}
                    </a>
                @endif
            </x-organomania.tr-responsive>
        @endif
        @if ($organBuilder->renovatedOrgans->isNotEmpty())
            <x-organomania.tr-responsive title="{{ __('Opravy') }}/{{ __('restaurování') }}">
                <div class="text-break items-list">
                    @foreach ($organBuilder->renovatedOrgans as $organ)
                        <x-organomania.organ-link :organ="$organ" :year="$organ->year_renovated ?? false" :isRenovation="true" :showShortPlace="true" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
                @if ($organBuilder->renovatedOrgans->count() > 1)
                    <a class="btn btn-sm btn-outline-secondary mt-1 me-1" href="{{ route('organs.index', ['filterRenovationOrganBuilderId' => $organBuilder->id]) }}">
                        <i class="bi bi-music-note-list"></i>
                        {{ __('Zobrazit vše') }}
                        <span class="badge text-bg-secondary rounded-pill">{{ $organBuilder->renovatedOrgans->count() }}</span>
                    </a>
                    <br class="d-sm-none" />
                @endif
            </x-organomania.tr-responsive>
        @endif

        @php
            $center = $organBuilder->getCenter();
            $inland = $organBuilder->isInland();
        @endphp
        @if ($this->relatedOrganBuilders->isNotEmpty() || $center || !$inland)
            <x-organomania.tr-responsive title="{{ __('Související varhanáři') }}">
                <div class="items-list">
                    @foreach ($this->relatedOrganBuilders as $relatedOrganBuilder)
                        <x-organomania.organ-builder-link :organBuilder="$relatedOrganBuilder" :showActivePeriod="true" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach

                    @if ($center)
                        @if ($this->relatedOrganBuilders->isNotEmpty()) <br /> @endif
                        <a
                            class="align-items-start link-primary text-decoration-none icon-link icon-link-hover"
                            href="{{ route('organ-builders.index', ['filterMunicipality' => $center]) }}"
                            wire:navigate
                        >
                            <i class="bi bi-person-circle"></i>
                            {{ $organBuilder->getCenterName() }}
                        </a>
                        @if ($organBuilderInCenterCount = $this->repository->getOrganBuilderInCenterCount($center))
                            <span class="badge text-bg-secondary rounded-pill">{{ $organBuilderInCenterCount }}</span>
                        @endif
                    @elseif (!$inland)
                        @if ($this->relatedOrganBuilders->isNotEmpty()) <br /> @endif
                        <a
                            class="align-items-start link-primary text-decoration-none icon-link icon-link-hover"
                            href="{{ route('organ-builders.index', ['filterRegionId' => -1]) }}"
                            wire:navigate
                        >
                            <i class="bi bi-person-circle"></i>
                            {{ __('Zahraniční varhanáři') }}
                        </a>
                        @if ($organBuilderNotInlandCount = $this->repository->getOrganBuilderNotInlandCount($center))
                            <span class="badge text-bg-secondary rounded-pill">{{ $organBuilderNotInlandCount }}</span>
                        @endif
                    @endif
                <div>
            </x-organomania.tr-responsive>
        @endif
        @if (isset($organBuilder->description))
            <tr id="description">
                <td colspan="2">
                    <div class="fs-5 mb-1 fw-semibold">{{ 'Popis' }}</div>
                    <div class="markdown">{!! $this->descriptionHtml !!}</div>
                </td>
            </tr>
        @endif
    </table>

    <div class="mb-4">
        @if ($organBuilder->isPublic())
            <div class="small text-secondary text-end mb-4">
                {{ __('Zobrazeno') }}: {{ Helpers::formatNumber($organBuilder->views) }}&times;
            </div>
        @endif

        @if (count($this->images) > 0)
            <div id="images" class="my-4">
                <x-organomania.gallery-carousel :images="$this->images" />
                @if ($organBuilder->isPublic() && count($this->imagesShownInCases) > 1)
                    <div class="text-center mt-2">
                        <a class="btn btn-sm btn-outline-secondary mt-1" href="{{ route('organs.cases', ['filterOrganBuilders' => [$organBuilder->id]]) }}" wire:navigate>
                            <i class="bi-camera"></i>
                            {{ __('Galerie skříní')}}
                            <span class="badge text-bg-secondary rounded-pill">{{ count($this->imagesShownInCases) }}</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="accordion">
        @if ($this->shouldShowFamilyTree)
            <x-organomania.accordion-item
                id="accordion-family-tree"
                class="d-print-none"
                title="{{ __('Rodokmen') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_FAMILY_TREE)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_FAMILY_TREE }}'); initFamilyTreeHelp()"
                noPadding
            >
                <div id="familyTree" style="height: {{ $this->familyTreeHeight }}px"></div>
            </x-organomania.accordion-item>
        @endisset

        @if ($this->shouldShowMap)
            @php $markerTitle = $this->municipalityCountry[0] . ' (' . __('sídlo dílny') . ')' @endphp
            <x-organomania.accordion-item
                id="accordion-map"
                class="d-print-none"
                title="{{ __('Mapa') }} ({{ $organBuilder->municipalityWithoutParenthesis }})"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_MAP)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_MAP }}')"
            >
                <x-organomania.map-detail :inland="$organBuilder->isInland()" :marker="$organBuilder" :title="$markerTitle" :otherMarkers="$this->mapOtherMarkers" />
                @if ($organBuilder->renovatedOrgans->isNotEmpty() || $this->mapOtherMarkers->isNotEmpty())
                    <div class="small text-center text-secondary mt-2">
                        @if ($organBuilder->renovatedOrgans->isNotEmpty())
                            {!! __('Varhanářem opravené/restaurované varhany jsou označeny <strong>šedě</strong>, ostatní <strong>bíle</strong>.') !!}
                        @else
                            {!! __('Varhanářem postavené varhany jsou označeny <strong>bíle</strong>.') !!}
                        @endif
                        <br />
                        {{ __('Podrobnosti o varhanách zobrazíte kliknutím na jejich bod na mapě.') }}
                    </div>
                @endif
            </x-organomania.accordion-item>
        @endisset

        @isset($organBuilder->literature)
            <x-organomania.accordion-item
                id="accordion-literature"
                title="{{ __('Literatura') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_LITERATURE)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_LITERATURE }}')"
            >
                <x-organomania.info-alert>
                    {{ __('Podrobný přehled literatury obsahuje strana')  }}
                    <a class="text-decoration-none" href="{{ route('publications.index') }}" wire:navigate>{{ __('Literatura o varhanách') }}</a>.
                </x-organomania.info-alert>

                <ul class="list-group list-group-flush small text-break">
                    @foreach (explode("\n", $organBuilder->literature) as $literature1)
                        <li @class(['list-group-item', 'd-flex', 'align-items-center', 'px-0', 'pt-0' => $loop->first, 'pb-0' => $loop->last])>
                            <span class="me-2">{!! Helpers::formatUrlsInLiterature($literature1) !!}</span>
                            @php $searchTerm = preg_replace('/ \([^()]*s(tr)?\.[^()]+\)/', '', $literature1) @endphp
                            <a
                                class="btn btn-sm btn-outline-secondary float-end ms-auto px-1"
                                target="_blank"
                                href="{{ Helpers::getGoogleSearchUrl($searchTerm) }}" style="font-size: 75%"
                                data-bs-toggle="tooltip"
                                data-bs-title="{{ __('Vyhledat literaturu pomocí Googlu') }}"
                              >
                                <i class="bi-search"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </x-organomania.accordion-item>
        @endisset
    </div>

    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" href="{{ $this->previousUrl }}" wire:navigate><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>&nbsp;
        @can('update', $organBuilder)
            <a class="btn btn-sm btn-outline-primary" href="{{ route('organ-builders.edit', ['organBuilder' => $organBuilder->id]) }}" wire:navigate>
                <i class="bi-pencil"></i> <span class="d-none d-sm-inline">{{ __('Upravit') }}</span>
            </a>
        @endcan
        <a class="btn btn-sm btn-outline-primary"  href="#" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $organBuilder->getShareUrl() }}">
            <i class="bi-share"></i> <span class="d-none d-sm-inline">{{ __('Sdílet') }}</span>
        </a>
    </div>

    <x-organomania.modals.categories-modal :categoriesGroups="$this->organBuilderCategoriesGroups" :categoryClass="OrganBuilderCategory::class" />

    <x-organomania.modals.share-modal />

    <x-organomania.modals.importance-hint-modal :title="__('Význam varhanáře')">
        {{ __('Význam varhanáře se eviduje, aby bylo možné množství varhanářů přibližně seřadit podle důležitosti.') }}
        {{ __('Význam je určen hrubým odhadem na základě řady kritérií a nejde o hodnocení kvality varhanáře.') }}
    </x-organomania.modals.importance-hint-modal>

</div>

@script
<script>
    let familyTreeItems = {{ Js::from($this->familyTreeItems) }}

    window.initFamilyTreeHelp = function () {
        initFamilyTree($wire, familyTreeItems)
    }

    setTimeout(() => {
        initFamilyTreeHelp()
    })
</script>
@endscript
