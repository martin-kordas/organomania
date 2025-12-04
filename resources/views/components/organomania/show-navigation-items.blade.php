@props(['navigationItems'])

<div class="list-group d-none d-lg-block position-fixed small text-end pe-4 navigation-items" style="top: 95px; transform: translate(-100%);">
    @foreach ($navigationItems as $anchor => $name)
        <button type="button" class="list-group-item list-group-item-action text-primary px-2 py-1 border-start-0 border-end-0 border-top-0 rounded-0" aria-current="true" onclick="scrollToElement({{ Js::from("#{$anchor}") }}, 75)">
            {{ $name }}
        </button>
    @endforeach
</div>