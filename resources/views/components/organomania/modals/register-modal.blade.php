@props(['registerName', 'pitch' => null, 'language' => null, 'categoriesAsLink' => false, 'excludeDispositionIds' => [], 'excludeOrganIds' => []])

@php
    use App\Models\RegisterName;
    $language ??= $registerName?->language;
    $register = $registerName?->register;
    if ($register) {
        $registerNames = $register->registerNames->filter(
            fn(RegisterName $registerName1) =>
                $registerName1->id !== $registerName->id
                && !$registerName1->isVisuallySameAs($registerName)
        )->unique(
            fn (RegisterName $registerName1) => $registerName1->getVisualIdentifier()
        );
    }
    $showPitches = $register && $register->registerPitches->isNotEmpty() && !(
        $register->registerPitches->count() === 1
        && $register->registerPitches->first()->getEnum() === $pitch
    );
@endphp

{{-- TODO: placeholdery pro případ, že se obsah déle načítá --}}
<div wire:ignore.self class="register-modal modal fade" id="registerModal" tabindex="-1" data-focus="false" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100" wire:loading.remove wire:target="setRegisterName">
                    @isset($register)
                        <h1 class="modal-title fs-5" id="registerModalLabel">
                            {{ $registerName->name }}
                            @if (!$registerName->hide_language)
                                <span class="text-body-secondary">({{ $registerName->language }})</span>
                            @endif
                        </h1>
                        <div @style(['columns: 2' => $registerNames->count() > 3])>
                            @foreach ($registerNames as $registerName1)
                                {{ $registerName1->name }}
                                @if (!$registerName1->hide_language)
                                    <span class="text-body-secondary">({{ $registerName1->language }})</span>
                                @endif
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
                <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>

            <div class="modal-body">
                @isset($register)
                    <div wire:loading.remove wire:target="setRegisterName">
                        <x-organomania.register
                            :$registerName :$registerNames :$register :$pitch :$showPitches :$language
                            :$categoriesAsLink :$excludeDispositionIds :$excludeOrganIds
                            :newTab="true"
                        />
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
