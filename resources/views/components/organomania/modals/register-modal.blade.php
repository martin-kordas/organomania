@props(['registerName', 'pitch' => null, 'language' => null, 'excludeDispositionIds' => [], 'excludeOrganIds' => []])

@php
    use App\Models\RegisterName;
    $language ??= $registerName?->language;
    $register = $registerName?->register;
    if ($register) {
        $registerNames = $register->registerNames->filter(
            fn(RegisterName $registerName1) => $registerName1->id !== $registerName->id
        );
    }
    $showPitches = $register && $register->registerPitches->isNotEmpty() && !(
        $register->registerPitches->count() === 1
        && $register->registerPitches->first()->getEnum() === $pitch
    );
@endphp

{{-- TODO: placeholdery pro případ, že se obsah déle načítá --}}
<div wire:ignore.self class="share-modal modal fade" id="registerModal" tabindex="-1" data-focus="false" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div wire:loading.remove wire:target="setRegisterName">
                    @isset($register)
                        <h1 class="modal-title fs-5" id="registerModalLabel">
                            {{ $registerName->name }}
                            <span class="text-body-secondary">({{ $registerName->language }})</span>
                        </h1>
                        <div>
                            @foreach ($registerNames as $registerName1)
                                {{ $registerName1->name }}
                                <span class="text-body-secondary">({{ $registerName1->language }})</span>
                                @if (!$loop->last) <br /> @endif
                            @endforeach
                        </div>
                    @endisset
                </div>
                <div wire:loading.block wire:target="setRegisterName" class="w-100">
                    <h1 class="card-title placeholder-glow">
                      <span class="placeholder col-6"></span>
                    </h1>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>

            <div class="modal-body">
                @isset($register)
                    <div wire:loading.remove wire:target="setRegisterName">
                        <div>
                            <span
                                class="badge text-bg-primary"
                                @if ($description = $register->registerCategory->getDescription()) data-bs-toggle="tooltip" data-bs-title="{{ $description }}" @endif
                            >
                                {{ $register->registerCategory->getName() }}
                            </span>
                            @foreach ($register->registerCategories as $category)
                                @php $description = $category->getEnum()->getDescription() @endphp
                                <span
                                    class="badge text-bg-secondary"
                                    @if ($description) data-bs-toggle="tooltip" data-bs-title="{{ $description }}" @endif
                                >
                                    {{ $category->getEnum()->getName() }}
                                </span>
                            @endforeach
                        </div>

                        @isset($register->description)
                            <div class="mt-2">
                                {{ $register->description }}
                            </div>
                            <hr>
                        @endisset

                        @isset($pitch)
                            <div class="mt-2">
                                {{ __('Poloha') }}: <em>{{ $pitch->getLabel($language) }}</em>
                                <small class="text-body-secondary">
                                    ({{ $pitch->getInterval() }} {{ __('poloha') }} &ndash; {{ __('na klávese') }} c<sup>1</sup> {{ __('zní tón') }} {!! $pitch->getAliquoteToneFormatted() !!})
                                </small>
                            </div>
                        @endisset

                        @if ($showPitches)
                            <div class="mt-2">
                                {{ __('Běžné polohy rejstříku') }}: <em>{{ $register->getPitchesLabels($language)->implode(', ') }}</em>
                            </div>
                        @endif

                        @php $dispositions = $register->getDispositions($excludeDispositionIds, $excludeOrganIds) @endphp
                        @if ($dispositions->isNotEmpty())
                            <div class="mt-2">
                                {{ __('Příklady v dispozicích') }}:
                                <ul class="mb-0">
                                    @foreach ($dispositions as $disposition)
                                        <li>
                                            <a href="{{ route('dispositions.show', [$disposition->slug, 'highlightRegisterId' => $registerName->register_id]) }}" class="link-primary text-decoration-none" target="_blank">
                                                {{ $disposition->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endisset

                <div wire:loading.block wire:target="setRegisterName">
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

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
            </div>
        </div>
    </div>
</div>
