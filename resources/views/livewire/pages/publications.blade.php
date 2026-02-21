<?php

use App\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Session;
use Livewire\Attributes\Locked;
use App\Interfaces\Category;
use App\Models\Author;
use App\Models\CustomCategory;
use App\Models\Publication;
use App\Models\Region;
use App\Enums\PublicationType;
use App\Enums\PublicationTopic;
use App\Enums\DispositionLanguage;
use App\Repositories\PublicationRepository;
use App\Traits\EntityPage;

new #[Layout('layouts.app-bootstrap')]
class extends Component {

    use EntityPage;

    #[Url(keep: true)]
    public $filterAll;
    #[Url(keep: true)]
    public $filterPublicationTypeId;
    #[Url(keep: true)]
    public $filterPublicationTopicId;
    #[Url(keep: true)]
    public $filterAuthorId;
    #[Url(keep: true)]
    public $filterJournal;
    #[Url(keep: true)]
    public $filterRegionId;
    #[Url(keep: true)]
    public $filterLanguage;
    #[Url(keep: true)]
    public $filterOnlineOnly;

    private PublicationRepository $repository;
    private Publication $model;

    const SORT_OPTIONS = [
        ['column' => 'publication_type_id', 'label' => 'Typ', 'type' => 'alpha'],
        ['column' => 'author', 'label' => 'Autor', 'type' => 'alpha'],
        ['column' => 'name', 'label' => 'Název', 'type' => 'alpha'],
        ['column' => 'place_of_publication', 'label' => 'Místo', 'type' => 'alpha'],
        ['column' => 'year', 'label' => 'Rok', 'type' => 'numeric'],
        ['column' => 'publication_topic_id', 'label' => 'Zaměření', 'type' => 'alpha'],
    ];

    public function boot(PublicationRepository $repository, Publication $model)
    {
        $this->viewTypes = ['table'];
        $this->bootCommon();

        $this->repository = $repository;
        $this->model = $model;

        $this->isLikeable = false;
        $this->isEditable = false;
        $this->isCategorizable = false;

        $this->createRoute = null;
        $this->exportRoute = 'publications.export';
        $this->customCategoriesRoute = null;
        $this->customCategoryRoute = null;
        $this->categorySelectPlaceholder = null;
        $this->gateUseCustomCategories = null;
        $this->gateLike = null;
        $this->entityPageViewComponent = 'publications-view';
        $this->entityClass = Publication::class;
        $this->entityNamePluralAkuzativ = __('podle názvu, autora, periodika');
        $this->filtersModalAutofocus = '#filterAll';
        $this->filters[] = 'filterAll';
        $this->filters[] = 'filterPublicationTypeId';
        $this->filters[] = 'filterPublicationTopicId';
        $this->filters[] = 'filterAuthorId';
        $this->filters[] = 'filterJournal';
        $this->filters[] = 'filterRegionId';
        $this->filters[] = 'filterLanguage';
        $this->filters[] = 'filterOnlineOnly';
        $this->heading = __('Literatura o varhanách<br />v České republice');
        $this->title = __('Literatura o varhanách v České republice');
    }

    public function mount()
    {
        Helpers::logPageViewIntoCache('publications');

        if (!request()->query('sortColumn')) $this->sortColumn = 'year';
        if (!request()->query('sortDirection')) $this->sortDirection = 'desc';
        if (!request()->query('perPage')) $this->perPage = 20;
        $this->mountCommon();
    }

    private function getCategoryEnum()
    {
        throw new \LogicException;
    }

    private function getOrganCategoryOrganCount(Category $category)
    {
        throw new \LogicException;
    }

    private function getOrganCustomCategoryOrganCount(CustomCategory $category)
    {
        throw new \LogicException;
    }

    #[Computed]
    public function publicationTypes()
    {
        return collect(PublicationType::cases())->mapWithKeys(
            fn($case) => [$case->value => $case->getName()]
        );
    }

    #[Computed]
    public function publicationTopics()
    {
        return collect(PublicationTopic::cases())->mapWithKeys(
            fn($case) => [$case->value => $case->getName()]
        );
    }

    #[Computed]
    public function languages()
    {
        return collect(DispositionLanguage::cases())->mapWithKeys(
            fn($case) => [$case->value => $case->getName()]
        );
    }

    #[Computed]
    public function authors()
    {
        return Author::withCount('publications')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->orderBy('year_of_birth')
            // zobrazení všech autorů, kteří napsali byť jen 1 článek, by bylo nepřehledné
            ->having('publications_count', '>', 1)
            ->get();
    }

    #[Computed]
    public function importantAuthors()
    {
        return $this->repository->getImportantAuthors(random: true);
    }

    #[Computed]
    public function importantAuthorsHint()
    {
        return $this->repository->getImportantAuthors(limit: 3);
    }

    #[Computed]
    public function journals()
    {
        return Publication::query()
            ->whereNotNull('journal')
            ->where('journal_is_book', 0)
            ->select('journal', DB::raw('count(*) as publications_count'))
            ->groupBy('journal')
            ->orderBy('journal')
            ->get();
    }

    #[Computed]
    public function regions()
    {
        return Region::query()->withCount('publications as count')->orderBy('name')->get();
    }

}; ?>

<div class="publications">
    <x-organomania.entity-page
        :metaDescription="__('Získejte přehled o bibliografii (knihách, článcích a další literatuře) týkající se varhan a varhanářů v České republice.')"/>

    <div class="text-center">
        <a class="link-primary text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#referencesModal">
            <small>{{ __('Další literatura') }}</small>
        </a>
    </div>

    <x-organomania.modals.references-modal title="{{ __('Další literatura') }}">
        <x-organomania.link-list>
            <x-organomania.link-list-item icon="link-45deg" url="https://www.varhany.net/literatura.php">
                varhany.net – {{ __('Bibliografie literatury')  }}
                <x-slot:description>{{ __('Přehled literatury použité v online databázi') }}</x-slot:description>
            </x-organomania.link-list-item>

            <x-organomania.link-list-item icon="book" url="https://www.cbdb.cz/kniha-255158-barokni-varhanarstvi-na-morave-dil-1-varhanari">
                Jiří Sehnal: Barokní varhanářství na Moravě – 1. Varhanáři
                <x-slot:description>SEHNAL, Jiří. Barokní varhanářství na Moravě. Vydání první. Brno: Muzejní a vlastivědná společnost v Brně, 2003-2018. 3 svazky. Prameny k dějinám a kultuře Moravy, č. 9, 10. Monografie. ISBN 80-7275-042-9. (s. 148–151)</x-slot>
            </x-organomania.link-list-item>

            <x-organomania.link-list-item icon="book" url="https://musicologica.upol.cz/pdfs/mus/2022/01/03.pdf">
                Bibliografie Jiřího Sehnala 1952–2022
                <x-slot:description>SPÁČILOVÁ, Jana. Bibliografie Jiřího Sehnala 1952-2022. Online. Musicologica Olomucensia. 2022, roč. 34, s. 19-42. ISSN 2787-9186. Dostupné z: https://doi.org/10.5507/mo.2022.003. [cit. 2026-02-12].</x-slot>
            </x-organomania.link-list-item>

            <x-organomania.link-list-item icon="book" url="https://theses.cz/id/v8c6cp/Bakalsk_prce_-Literatura_o_varhanch_na_Morav.pdf">
                Veronika Kohutová: Literatura o varhanách na Moravě
                <x-slot:description>KOHUTOVÁ, Veronika. Literatura o varhanách na Moravě. Online. Bakalářská práce. Olomouc: Univerzita Palackého v Olomouci, Pedagogická fakulta. 2015. Dostupné z: https://theses.cz/id/v8c6cp/.</x-slot>
            </x-organomania.link-list-item>
        </x-organomania.link-list>
    </x-organomania.modals.references-modal>
</div>
