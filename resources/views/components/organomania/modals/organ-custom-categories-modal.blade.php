@props(['customCategories'])

<div wire:ignore.self class="cutom-categories-modal modal fade" id="customCategoriesModal" tabindex="-1" data-focus="false" aria-labelledby="customCategoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form wire:submit="saveOrganCustomCategories">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="customCategoriesModalLabel">{{ __('Vlastní kategorie') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
                </div>
                <div wire:loading.block>
                    <x-organomania.spinner />
                </div>
                <div wire:loading.remove>
                    <div class="modal-body">
                        @if ($this->organCustomCategories->isEmpty())
                            <div class="text-center">
                                <small class="text-body-secondary">
                                    {{ __('Zatím nebyly definovány žádné vlastní kategorie.') }}
                                    <br />
                                    <a class="link-primary text-decoration-none" href="{{ route($this->customCategoriesRoute) }}">{{ __('Přidat kategorii') }}</a>
                                </small>
                            </div>
                        @else
                            <select id="organCustomCategoriesIds" class="form-select select2" wire:model="organCustomCategoriesIds" data-placeholder="{{ __('Zadejte kategorie varhan') }}&hellip;" multiple aria-label="{{ __('Vlastní kategorie') }}">
                                @foreach ($this->organCustomCategories as $category)
                                    <option value="{{ $category->id }}" title="{{ $category->getDescription() }}">
                                        {{ __($category->name) }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if ($this->organCustomCategories->isNotEmpty())
                            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi-floppy"></i> {{ __('Uložit') }}</button>
                        @endif
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>