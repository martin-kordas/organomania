@props(['href'])

<li class="nav-item">
    <a {{ $attributes->merge(['href' => $href, 'class' => 'nav-link px-2 text-body-secondary']) }}>
        {{ $slot }}
    </a>
</li>
