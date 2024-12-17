@use(App\Services\DispositionParser)

<div class="modal fade" id="dispositionTextExampleModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="importTextExampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="importTextExampleModalLabel">{{ __('Příklad dispozice') }}</h1>
                <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#importDispositionFromTextModal" aria-label="{{ __('Zavřít') }}"></button>
            </div>
            <div class="modal-body" style="white-space: pre">{{ DispositionParser::DISPOSITION_TEXT_EXAMPLE }}</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importDispositionFromTextModal">{{ __('Zavřít') }}</button>
            </div>
        </div>
    </div>
</div>