@props(['additionalImage'])

<div>
    <strong style="font-size: 115%">{{ $additionalImage->municipality }}</strong> &nbsp;|&nbsp; {{ $additionalImage->place }}
</div>

<div>
    {{ $additionalImage->organBuilderName }}
    @if (!empty($additionalImage->allDetails))
        <span style='color: grey'>({{ implode(', ', $additionalImage->allDetails) }})</span>
    @endif
</div>
