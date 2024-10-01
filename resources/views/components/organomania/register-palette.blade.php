@php
  use App\Enums\RegisterCategory;
@endphp

<div class="offcanvas offcanvas-end" tabindex="-1" id="registerPalette" aria-labelledby="registerPaletteLabel" data-bs-scroll="true" data-bs-backdrop="false" wire:ignore.self @keydown.esc="closeRegisterPalette()">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="registerPaletteLabel">
            {{ __('Paleta rejstříků') }}
            @isset ($this->paletteKeyboardIndex)
                &ndash; {{ $this->getKeyboardName($this->paletteKeyboardIndex) }}
            @endisset
        </h5>
        <button id="registerPaletteClose" type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Zavřít') }}"></button>
    </div>
    <div class="offcanvas-body pt-0">
        <div class="body-inner">
            @isset($this->paletteKeyboardIndex)
                <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                    @foreach ($this->paletteItemGroups as $categoryId => $paletteItems)
                        <li class="nav-item" role="presentation">
                            <button
                                @class(['nav-link', 'px-2', 'py-1', 'active' => $this->isTabSelected($categoryId)])
                                id="registerPaletteTab{{ $categoryId }}"
                                data-bs-toggle="tab"
                                data-bs-target="#registerPaletteTabPane{{ $categoryId }}"
                                wire:click="selectPaletteTab({{ $categoryId }})"
                                type="button"
                                role="tab"
                            >
                                {{ $this->getPaletteItemGroupName($categoryId) }}
                                @if (!empty($paletteItems))
                                    <span class="badge text-bg-secondary rounded-pill" >{{ count($paletteItems) }}</span>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach ($this->paletteItemGroups as $categoryId => $paletteItems)
                        <div
                            @class(['tab-pane', 'fade', 'show' => $this->isTabSelected($categoryId), 'active' => $this->isTabSelected($categoryId)])
                            id="registerPaletteTabPane{{ $categoryId }}"
                            role="tabpanel"
                            tabindex="0"
                        >
                            @if (empty($paletteItems))
                                <p class="text-center d-block">
                                    <small class="text-body-tertiary">{{ __('V této kategorii nejsou žádné dosud nevložené rejstříky.') }}</small>
                                </p>
                            @else
                                <x-organomania.info-alert class="mb-3 d-print-none">
                                    {{ __('Kliknutím na rejstřík jej vložíte do dispozice.') }}
                                </x-organomania.info-alert>
                                @php
                                    $subgroups = $categoryId === 0 && !$this->getPaletteKeyboard()['pedal']
                                        ? $this->getPaletteItemSubgroups($paletteItems)
                                        : [$paletteItems];
                                @endphp
                                @foreach ($subgroups as $categoryId => $subgroup)
                                    @if (count($subgroups) > 1)
                                        <h6 class="mt-3">{{ RegisterCategory::from($categoryId)->getName() }}</h6>
                                    @endif
                                    <div class="btn-group-vertical" style="min-width: 11em;">
                                        @foreach ($subgroup as $paletteItem)
                                            <div class="btn-group">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-dark w-100 text-start"
                                                    @click="$wire.addRegisterFromPalette({{ $paletteItem->registerName->id }}, {{ $paletteItem->pitch->value }}, {{ $paletteItem->multiplier }}); focusPalette()"
                                                >
                                                    {{ $paletteItem->getRegisterName() }}
                                                    @isset ($paletteItem->multiplier)
                                                        {{ $paletteItem->multiplier }}&times;
                                                    @endisset
                                                    <span class="float-end ms-2">{{ $paletteItem->getPitchLabel() }}</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-dark btn-sm"
                                                    wire:click="setRegisterName({{ $paletteItem->registerName->id }})"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#registerModal"
                                                >
                                                    <span data-bs-toggle="tooltip" data-bs-title="{{ __('Podrobnosti o rejstříku') }}">
                                                        <i class="bi-eye"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>
            @endisset
        </div>
    </div>
</div>

@script
<script>
    window.closeRegisterPalette = function() {
        $("#registerPaletteClose").click();
    }
</script>
@endscript