@props(['color', 'icon'])

<div {{ $attributes->merge(['class' => "alert alert-$color py-1 px-2"]) }}>
    <small>
        <i class="bi-{{ $icon }}"></i>
        {{ $slot }}
    </small>
</div>
