@props(['title', 'id', 'show' => false, 'onclick' => null])

<div class="accordion-item">
    <h2 class="accordion-header">
        <button
            @class(['accordion-button', 'py-2', 'collapsed' => !$show])
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#{{ $id }}"
            aria-expanded="true"
            aria-controls="{{ $id }}"
            @isset($onclick) @click="{{ $onclick }}" @endisset
        >
            {{ $title }}
        </button>
    </h2>
    <div id="{{ $id }}" @class(['accordion-collapse', 'collapse', 'show' => $show])>
        <div class="accordion-body">
            {{ $slot }}
        </div>
    </div>
</div>