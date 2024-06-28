@props(['organs'])

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4 mb-4 align-items-center">
    @foreach ($organs as $organ)
    <div class="col">
        <div class="card shadow-sm m-auto" style="width: 23em;">
            <div class="position-relative" style="width: 23em;">
                <img src="{{ $organ->image_url }}" style="width: calc(100% - 2px)" />
                <img width="125" class="region position-absolute start-0 m-2 bottom-0" src="{{ Vite::asset("resources/images/regions/{$organ->region_id}.png") }}" />
            </div>
            <div class="card-header">
                <h5 class="card-title"><strong>{{ $organ->municipality }}</strong> | {{ $organ->place }}</h5>
                <div class="fst-italic mb-1">
<!--                            <span data-bs-toggle="popover" data-bs-trigger="hover" data-bs-toggle="popover" data-bs-title="Michael ENGLER (1688-1760)" data-bs-content="Významný vratislavský varhanář, působící především ve Slezsku. V našich zemích se proslavil stavbou varhan v kostele sv. Mořice v Olomouci.">Michael ENGLER (1745)</span><br />-->
                    <span data-bs-toggle="popover" data-bs-trigger="hover focus" @if ($organ->organBuilder->perex) data-bs-toggle="popover" data-bs-title="{{ $organ->organBuilder->name }} ({{ $organ->organBuilder->municipality }})" data-bs-content="{{ $organ->organBuilder->perex }} @endif">
                        {{ $organ->organBuilder->name }} <span class="text-secondary">({{ $organ->year_built }})</span>
                    </span>
                </div>
                <div class="stars">
                    <span class="text-body-secondary">{{ $organ->manuals_count }} manuály / {{ $organ->stops_count }} rejstříků</span>
                    <x-organomania.stars class="float-end" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Význam" :count="round($organ->importance / 2)" />

                </div>
            </div>
            <div class="card-body">
                <div class="list-group-item">
                    @foreach ($organ->organCategories as $category)
                        @if (!$category->getEnum()->isPeriodCategory())
                            <x-organomania.category-badge :category="$category->getEnum()" />
                        @endif
                    @endforeach
                    @if (isset($organ->perex))
                        <p class="card-text mt-2">{{ $organ->perex }}</p>
                    @endif
                </div>
            </div>
            <div class="card-footer text-body-secondary">
                <div class="d-flex justify-content-between align-items-center">
                    <a type="button" class="btn btn-sm btn-primary" href="{{ route('organs.show', ['organ' => $organ->id]) }}"><i class="bi-eye"></i> Zobrazit</a>
                    <div class="btn-group">
                        <a type="button" class="btn btn-sm btn-outline-primary z-1 disabled"><i class="bi-pencil"></i> Upravit</a>
                        <a type="button" class="btn btn-sm btn-outline-primary z-1 disabled" data-bs-toggle="tooltip" data-bs-title="Sdílet"><i class="bi-share"></i></a>
                    </div>
                    <div class="btn-group">
                        <a type="button" @class(['btn', 'btn-sm', 'rounded-pill', 'z-1', 'btn-danger' => $organ->my_organ_likes_count > 0, 'btn-outline-danger' => $organ->my_organ_likes_count <= 0]) wire:click="likeToggle({{ $organ->id }})" data-bs-toggle="tooltip" data-bs-title="Přidat do oblíbených"><i class="bi-heart"></i> {{ $organ->organ_likes_count }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>