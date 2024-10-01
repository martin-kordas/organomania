@props(['title', 'code' => null])

<div class="w-100 h-100">
    <h3>
        <i class="bi-exclamation-octagon"></i>
        @if ($code) {{ $code }} | @endif
        {{ $title }}
    </h3>
  
    <div class="text-secondary fs-5">
        {{ __('Omlouváme se za vzniklé potíže.') }}
    </div>
</div>
