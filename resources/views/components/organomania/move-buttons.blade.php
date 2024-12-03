@props(['actionUp', 'actionDown', 'moveWhat' => null, 'isFirst' => false, 'isLast' => false, 'disabled' => false])

<div {{ $attributes->merge(['class' => 'btn-group']) }}>
    <button
        type="button"
        @class(['btn', 'btn-outline-secondary', 'btn-sm', 'disabled' => $disabled || $isFirst])
        wire:click="{{ $actionUp }}"
        data-bs-toggle="tooltip"
        data-bs-title="{{ __("Přesunout $moveWhat nahoru") }}"
    >
        <i class="bi-chevron-up"></i>
    </button>
    <button
        type="button"
        @class(['btn', 'btn-outline-secondary', 'btn-sm', 'disabled' => $disabled || $isLast])
        wire:click="{{ $actionDown }}"
        data-bs-toggle="tooltip"
        data-bs-title="{{ __("Přesunout $moveWhat dolů") }}"
    >
        <i class="bi-chevron-down"></i>
    </button>
</div>
