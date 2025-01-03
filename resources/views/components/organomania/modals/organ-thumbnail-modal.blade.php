@props(['showOrgansTimeline' => false])

<div wire:ignore.self class="modal organ-thumbnail-modal fade" id="organThumbnail" tabindex="-1" data-focus="false" aria-labelledby="organThumbnailLabel" aria-hidden="true">
    <div class="modal-dialog shadow-lg" wire:key="{{ $this->thumbnailOrgan->id ?? 0 }}">
        <div class="modal-content">
            <x-dynamic-component :component="$this->thumbnailComponent" :organ="$this->thumbnailOrgan" :modal="true" :$showOrgansTimeline />
        </div>
    </div>
</div>
