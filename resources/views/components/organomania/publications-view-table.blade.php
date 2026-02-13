@props(['organs'])

@use(App\Enums\DispositionLanguage)
@use(App\Enums\PublicationTopic)
@use(App\Helpers)
@use(App\Http\Controllers\ThumbnailController)

<div>
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle">
            <thead>
                <tr>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('author')" />
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('publication_type_id')" class="text-center" />
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('name')" class="name" :sticky="true" />
                    <th>&nbsp;</th>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('place_of_publication')" class="d-none d-lg-table-cell" />
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('year')" class="text-end" />
                    <th class="text-center">{{ __('Kraj') }}</th>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('publication_topic_id')" class="text-center" />
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach ($organs as $publication)
                    <tr>
                        <td>
                            @foreach ($publication->authors as $author)
                                <a
                                    class="text-decoration-none text-nowrap"
                                    href="{{ route('publications.index', ['filterAuthorId' => $author->id]) }}"
                                    wire:navigate
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $author->fullNameReverseWithYears }}"
                                >
                                    {{ $author->initialsName }}
                                </a>
                                @if (!$loop->last) <br /> @endif
                            @endforeach
                        </td>
                        <td class="text-center table-light fs-5">
                            @if ($publication->publicationType)
                                <a href="{{ route('publications.index', ['filterPublicationTypeId' => $publication->publicationType->value]) }}" wire:navigate>
                                    <i
                                        class="bi {{ $publication->publicationType->getIcon() }}"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="{{ $publication->publicationType->getName() }}"
                                    ></i>
                                </a>
                            @endif
                        </td>
                        <td class="name table-light position-sticky start-0">
                            @php $url = $publication->url ?? $publication->library_url @endphp
                            <span class="fw-semibold" @isset($publication->name_cz) title="{{ __('Původní název') }}: {{ $publication->name }}" @endisset>
                                @isset($publication->url)
                                    <i class="bi-download text-secondary"></i>
                                @endisset
                                @if ($publication->language !== DispositionLanguage::Czech)
                                    <span class="emoji">{!! $publication->language->getFlagEmoji() !!}</span>
                                @endif
                                @isset($url)
                                    <a class="link-dark link-underline-opacity-25 link-underline-opacity-75-hover" href="{{ $url }}" target="_blank">
                                @endisset
                                <span class="d-none d-md-inline">{{ $publication->displayedName }}</span>
                                <span class="d-md-none" data-bs-toggle="tooltip" data-bs-title="{{ $publication->displayedName }}">{{ str($publication->displayedName)->limit(65) }}</span>
                                @isset($url)
                                    </a>
                                @endisset
                            </span>
                            @if ($publication->journal)
                                <div class="small text-secondary">
                                    In:
                                    @if ($publication->journal_is_book)
                                        {{ $publication->journal }}
                                    @else
                                        <a class="text-decoration-none" href="{{ route('publications.index', ['filterJournal' => $publication->journal]) }}" wire:navigate>
                                            {{ $publication->journal }}
                                        </a>
                                    @endif
                                    @isset($publication->journal_issue)({{ $publication->journal_issue }})@endisset
                                </div>
                            @elseif ($publication->thesis_description)
                                <div class="small text-secondary">
                                    {{ $publication->thesis_description }}
                                </div>
                            @elseif ($publication->page_count)
                                <div class="small text-secondary">
                                    {{ __('Kniha') }}, {{ $publication->page_count }} {{ __('stran') }}
                                </div>
                            @endif
                        </td>
                        <td class="table-light">
                            @isset($publication->organ)
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('organs.show', $publication->organ->slug) }}" target="_blank" data-bs-toggle="tooltip" data-bs-title="{{ __("Zobrazit varhany v&nbsp;Organomanii") }}" data-bs-html="true">
                                    <i class="bi-music-note-list"></i>
                                </a>
                            @endisset
                            @isset($publication->organBuilder)
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('organ-builders.show', $publication->organBuilder->slug) }}" target="_blank" data-bs-toggle="tooltip" data-bs-title="{{ __("Zobrazit varhanáře v&nbsp;Organomanii") }}" data-bs-html="true">
                                    <i class="bi-person-circle"></i>
                                </a>
                            @endisset
                        </td>
                        <td class="d-none small d-lg-table-cell">
                            {{ $publication->place_of_publication }}
                        </td>
                        <td class="text-end">
                            <span @class(['mark' => $publication->year >= now()->year - 1])>{{ $publication->year }}</span>
                        </td>
                        <td class="text-center" @isset($publication->region) data-bs-toggle="tooltip" data-bs-title="{{ $publication->region?->name }}" @endisset>
                            @isset($publication->region_id)
                                <a href="{{ route('publications.index', ['filterRegionId' => $publication->region_id]) }}" wire:navigate>
                                    <img width="70" class="region me-1" src="{{ Vite::asset("resources/images/regions/{$publication->region_id}.png") }}" />
                                </a>
                            @endisset
                        </td>
                        <td class="text-center">
                            @if ($publication->publicationTopic && $publication->publicationTopic !== PublicationTopic::Other)
                                <a
                                    class="badge text-decoration-none text-bg-primary"
                                    href="{{ route('publications.index', ['filterPublicationTopicId' => $publication->publicationTopic->value]) }}"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $publication->publicationTopic->getDescription() }}"
                                    wire:navigate
                                >
                                    {{ $publication->publicationTopic->getName() }}
                                </a>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button
                                    class="btn btn-sm btn-outline-primary text-nowrap"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ __('Zkopírovat citaci') . "<br />" . "(ČSN ISO 690)" }}"
                                    data-bs-html="true"
                                    onclick="publicationsViewTable.copyCitationToClipboard({{ Js::from($publication->getCitation())  }})"
                                >
                                    <i class="bi-copy"></i>
                                </button>
                                @isset($publication->library_url)
                                    <a class="btn btn-sm btn-outline-primary text-nowrap" data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit podrobnosti') }}" href="{{ $publication->library_url }}" target="_blank">
                                        <i class="bi-box-arrow-up-right"></i>
                                    </a>
                                @endisset
                                @isset($publication->url)
                                    <a class="btn btn-sm btn-primary text-nowrap" data-bs-toggle="tooltip" data-bs-title="{{ __('Stáhnout/otevřít online') }}" href="{{ $publication->url }}" target="_blank">
                                        <i class="bi-download"></i>
                                    </a>
                                @endisset
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-organomania.toast toastId="citationCopiedToast">
        {{ __('Citace byla úspěšně zkopírována do schránky.') }}
    </x-organomania.toast>
</div>

@script
<script>
    window.publicationsViewTable = {}

    window.publicationsViewTable.copyCitationToClipboard = async function (citation) {
        await copyToClipboard(citation)

        var toast = $('#citationCopiedToast')[0]
        var bootstrapToast = bootstrap.Toast.getOrCreateInstance(toast)
        bootstrapToast.show()
    }
</script>
@endscript
