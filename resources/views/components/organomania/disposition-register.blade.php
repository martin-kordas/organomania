@props([
    'register', 'keyboard', 'disposition',
    'isEdit', 'checked', 'highlighted', 'invisible' => false,
    'keyboardsInSeparateColumns' => true, 'showKeyboard' => false,
])

@php
    use App\Enums\RegisterCategory;
    use App\Models\DispositionRegister;
@endphp

<li
    @class(['rounded', 'disposition-item', 'disposition-item-padding', 'register', 'checked' => $checked, 'highlighted' => $highlighted, 'd-none' => $invisible])
    @if ($isEdit)
        style="cursor: pointer"
        onclick="registerLiOnclick(event)"
    @elseif ($register->register_name_id)
        data-bs-toggle="modal"
        data-bs-target="#registerModal"
        style="cursor: pointer"
        wire:click="setRegisterName({{ $register->register_name_id }}, {{ $register->pitch?->value }})"
    @endif
    wire:key="keyboard{{ $keyboard->id }}_register{{ $register->id }}" 
>
    <div class="hstack gap-2">
        @if ($isEdit)
            <input wire:key="checkbox{{ $keyboard->id }}_register{{ $register->id }}" class="form-check-input d-print-none" type="checkbox" wire:model.change="dispositionRegisters.{{ $register->id }}" />
        @endif
        <div @class(['coupler' => $register->coupler, 'lh-sm' => isset($this->translationLanguage)])>
            @php
                $name = $this->getDispositionRegisterName($register);
                $nameTranslated = $this->getDispositionRegisterName($register, translate: true);
            @endphp
            {{ $nameTranslated }}
            @isset($register->multiplier)
                {{ DispositionRegister::formatMultiplier($register->multiplier) }}
            @endisset
            @if ($showKeyboard)
                <span class="text-body-tertiary" data-bs-toggle="tooltip" data-bs-title="{{ $keyboard->getFullName() }}">
                    ({{ $keyboard->getAbbrev() }})
                </span>
            @endif
            @if ($nameTranslated !== $name)
                <br />
                <small class="text-body-tertiary">{{ $name }}</small>
            @endif
        </div>

        <div class="ms-auto"></div>
        @if ($this->reedsWithDot && $register->register?->registerCategory === RegisterCategory::Reed)
            &bull;
        @endif
        @isset($register->pitch)
            <div>
                {{ $register->pitch->getLabel($this->preferredLanguage) }}
            </div>
        @endisset
        <a
            @isset ($register->registerName) href="{{ route('dispositions.registers.show', $register->registerName->slug) }}" @endisset
            @class(['register-info-btn', 'show-description' => !$keyboardsInSeparateColumns, 'position-relative', 'btn', 'btn-outline-secondary', 'btn-sm', 'd-print-none', 'invisible' => !$register->register_name_id])
            wire:click="setRegisterName({{ $register->register_name_id }}, {{ $register->pitch?->value }})"
            data-bs-toggle="modal"
            data-bs-target="#registerModal"
            data-description="{{ str($register->register?->description) }}"
        >
            <span data-bs-toggle="tooltip" data-bs-title="{{ __('Podrobnosti o rejstříku') }}">
                <i class="bi-eye"></i>
            </span>
        </a>
    </div>
</li>