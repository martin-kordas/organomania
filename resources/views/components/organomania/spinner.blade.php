@props(['margin' => true])

<div {{ $attributes->class(['text-secondary', 'd-flex', 'justify-content-center', 'my-5' => $margin]) }} role="status">
    <div class="spinner-border" role="status">
        <span class="visually-hidden">{{ __('Načítání') }}&hellip;</span>
    </div>
</div>
