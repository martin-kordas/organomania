<div {{ $attributes->merge(['class' => 'alert alert-light py-1 px-2']) }}>
    <small>
        <i class="bi-info-circle"></i>
        {{ $slot }}
    </small>
</div>
