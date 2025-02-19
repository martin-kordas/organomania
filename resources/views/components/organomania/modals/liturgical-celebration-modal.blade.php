@props(['liturgicalCelebration'])

@use(App\Helpers)

<div wire:ignore.self class="liturgical-celebration-modal modal fade" id="liturgicalCelebrationModal" tabindex="-1" data-focus="false" aria-labelledby="liturgicalCelebrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100" wire:loading.remove wire:target="setLiturgicalCelebration">
                    @isset($liturgicalCelebration)
                        <div class="small">{{ Helpers::formatDate($liturgicalCelebration->liturgicalDay->date) }}</div>
                        <h1 class="modal-title fs-5" id="liturgicalCelebrationModalLabel"  @if (Auth::user()?->admin) title="ID: {{ $liturgicalCelebration->id }}" @endif>
                            <i class="bi bi-{{ $liturgicalCelebration->getIcon() }}" style="color: {{ $liturgicalCelebration->getIconColor() }}"></i>
                            {{ $liturgicalCelebration->name }}
                        </h1>
                        @if (!in_array($liturgicalCelebration->rank, ['neděle', 'ferie']))
                            <div class="text-body-secondary">{{ $liturgicalCelebration->rank }}</div>
                        @endif
                    @endisset
                </div>
                <div wire:loading.block wire:target="setLiturgicalCelebration" class="w-100">
                    <h1 class="card-title placeholder-glow">
                      <span class="placeholder col-6"></span>
                    </h1>
                </div>
                <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>

            <div class="modal-body">
                @isset($liturgicalCelebration)
                    <div class="items-list" wire:loading.remove wire:target="setLiturgicalCelebration">
                        <div class="list-group">
                            <a href="{{ $liturgicalCelebration->liturgicalDay->getCalendarUrl() }}" class="list-group-item list-group-item-action link-primary" target="_blank">
                                <i class="bi bi-link-45deg"></i>
                                {{ __('Liturgický kalendář') }}
                                <small class="d-block text-body-secondary">
                                    <span class="d-block">
                                        {{ __('Období') }}: {{ $liturgicalCelebration->liturgicalDay->getSeasonLocalized() }}
                                    </span>
                                    @isset($liturgicalCelebration->liturgicalDay->lectionary)
                                        <span class="d-block">
                                            {{ __('Nedělní cyklus') }}: {{ $liturgicalCelebration->liturgicalDay->lectionary }}
                                        </span>
                                    @endisset
                                    @if (!$liturgicalCelebration->liturgicalDay->isSunday())
                                        <span class="d-block">
                                            @isset($liturgicalCelebration->liturgicalDay->ferial_lectionary)
                                                {{ __('Feriální cyklus') }}: {{ $liturgicalCelebration->liturgicalDay->ferial_lectionary }}
                                            @endisset
                                        </span>
                                    @endif
                                </small>
                            </a>
                            
                            @isset($liturgicalCelebration->olejnikPsalm)
                                <a href="{{ $liturgicalCelebration->psalmOlejnikUrl }}" class="list-group-item list-group-item-action link-primary" target="_blank">
                                    <i class="bi bi-link-45deg"></i>
                                    {{ __('Žalm') }} &ndash; Olejník: OL{{ $liturgicalCelebration->olejnikPsalm->number }}
                                    <small class="d-block text-body-secondary">
                                        {{ $liturgicalCelebration->olejnikPsalm->name }}
                                    </small>
                                </a>
                            @endisset

                            @isset($liturgicalCelebration->psalm_korejs)
                                <a href="{{ $liturgicalCelebration->psalm_korejs }}" class="list-group-item list-group-item-action link-primary" target="_blank">
                                    <i class="bi bi-link-45deg"></i>
                                    {{ __('Žalm') }} &ndash; Korejs
                                </a>
                            @endisset
                        </div>
                    </div>
                @endisset

                <div wire:loading.block wire:target="setLiturgicalCelebration">
                    <span class="placeholder col-7"></span>
                    <span class="placeholder col-4"></span>
                    <span class="placeholder col-4"></span>
                    <span class="placeholder col-6"></span>
                    <span class="placeholder col-8"></span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
            </div>
        </div>
    </div>
</div>
