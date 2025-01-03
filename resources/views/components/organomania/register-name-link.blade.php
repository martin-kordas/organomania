@props(['registerName', 'newTab' => false])

<a
    class="register-link icon-link icon-link-hover align-items-start link-primary text-decoration-none"
    href="{{ route('dispositions.registers.show', [$registerName->slug]) }}"
    @if ($newTab) target="_blank" @else wire:navigate @endif
>
    <i class="bi bi-record-circle"></i>
    <span>
        {{ $registerName->name }}
    </span>
    <span class="text-body-secondary">
        ({{ $registerName->register->registerCategory->getName() }})
    </span>
</a>
