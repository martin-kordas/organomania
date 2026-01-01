@props(['href'])

<li class="nav-item">
    <a {{ $attributes->merge(['href' => $href, 'class' => 'nav-link position-relative px-1 px-md-2 py-1 text-body-secondary']) }}>
        {{ $slot }}
    </a>
</li>
