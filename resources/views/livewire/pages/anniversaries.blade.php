<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use App\Helpers;
use App\Services\AnniversariesService;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    #[Url(keep: true)]
    public int $step = AnniversariesService::DEFAULT_STEP;

    private AnniversariesService $anniversariesService;

    private int $currentYear;
    private array $years;

    public function boot(AnniversariesService $anniversariesService)
    {
        $this->anniversariesService = $anniversariesService;
        $this->currentYear = now()->year;
        $this->years = range($this->currentYear - 1, $this->currentYear + 3);
    }

    public function mount()
    {
        Helpers::logPageViewIntoCache('anniversaries');
    }

    public function rendering(View $view): void
    {
        $view->title(__('Varhanní výročí'));
    }

    public function rendered()
    {
        $this->dispatch("bootstrap-rendered");
    }

    #[Computed]
    private function data()
    {
        return $this->getData($this->step);
    }

    #[Computed]
    private function dataStep50()
    {
        if ($this->step === 50) return $this->data;
        
        return $this->getData(50);
    }

    private function getData(int $step)
    {
        $data = [];
        foreach ($this->years as $year) {
            $data[$year] = $this->anniversariesService->getAnniversaries($year, $step);
        }
        return $data;
    }

    private function shouldHighlightAnniversary(int $anniversaryYear, int $year)
    {
        return (($year - $anniversaryYear) % 50 === 0);
    }
}; ?>

<div class="anniversaries container">
    @push('meta')
        <meta name="description" content="{{ __('Objevte výročí významných varhan a varhanářů, která proběhnou v letošním roce a v následujících rocích.') }}">
    @endpush

    <div class="anniversaries container">
        <h3>
            <i class="bi bi-cake2"></i>
            {{ __('Varhanní výročí') }}
        </h3>

        <div class="mt-3 mb-4">
            <label class="form-check-label radio-label">{{ __('Výročí po') }}</label>
            @foreach ([10, 50] as $step)
                <input
                    type="radio"
                    class="btn-check"
                    wire:model.change="step"
                    value="{{ $step }}"
                    id="step{{ $step }}"
                >
                <label class="btn btn-sm btn-outline-secondary" for="step{{ $step }}">
                    {{ $step }} {{ __('letech') }}
                </label>
            @endforeach
        </div>
        
        <ul class="nav nav-tabs" id="yearTabs" role="tablist">
            @foreach ($this->dataStep50 as $year => $anniversaries)
                <li class="nav-item" role="presentation">
                    <button
                        @class(['nav-link', 'active' => $year === $this->currentYear])
                        id="yearTab{{ $year }}"
                        data-bs-toggle="tab" data-bs-target="#yearContent{{ $year }}"
                        type="button"
                        role="tab" aria-controls="yearContent{{ $year }}" aria-selected="true"
                    >
                        <div class="position-relative pe-2">
                            {{ $year }}
                            @if ($anniversaries['count'] > 0)
                                <span class="info-count-badge position-absolute top-0 start-100 translate-middle">
                                    <span class="badge rounded-pill text-bg-secondary" style="font-size: 55%;">
                                        {{ $anniversaries['count'] }}
                                    </span>
                                </span>
                            @endif
                        </div>
                    </button>
                </li>
            @endforeach
        </ul>
        <div class="tab-content mt-3" id="yearsContent">
            @foreach ($this->data as $year => $anniversaries)
                <div
                    @class(['tab-pane', 'fade', 'show' => $year === $this->currentYear, 'active' => $year === $this->currentYear])
                    id="yearContent{{ $year }}"
                    role="tabpanel" aria-labelledby="yearTab{{ $year }}" tabindex="0"
                >
                    @if ($anniversaries['count'] <= 0)
                        <div>{{ __('V tomto roce nebylo nalezeno žádné výročí.') }}</div>
                    @else
                        @if ($anniversaries['organs']->isNotEmpty())
                            <h4>{{ __('Výročí varhan') }}</h4>
                            <table class="table table-hover table-sm w-auto">
                                @foreach ($anniversaries['organs'] as $organ)
                                    <x-organomania.anniversary-rows :entity="$organ" :$year>
                                        <x-slot:entity-link>
                                            <div class="d-none d-md-block">
                                                <x-organomania.organ-link :organ="$organ" :year="false" />
                                            </div>
                                            <div class="d-md-none">
                                                <x-organomania.organ-link :organ="$organ" :year="false" showShortPlace />
                                            </div>
                                        </x-slot>
                                    </x-organomania.anniversary-rows>
                                @endforeach
                            </table>

                            <div class="mb-4">
                                @php $ids = $anniversaries['organs']->pluck('id')->toArray()  @endphp
                                <a class="btn btn-sm btn-outline-secondary mt-1 me-1" href="{{ route('organs.index', ['filterId' => $ids, 'viewType' => 'map']) }}">
                                    <i class="bi bi-music-note-list"></i>
                                    {{ __('Zobrazit vše') }}
                                    <span class="badge text-bg-secondary rounded-pill">{{ $anniversaries['organs']->count() }}</span>
                                </a>
                            </div>
                        @endif
                        
                        @if ($anniversaries['timelineItems']->isNotEmpty())
                            <h4>{{ __('Výročí varhanářů') }}</h4>
                            <table class="table table-hover table-sm w-auto">
                                @foreach ($anniversaries['timelineItems'] as $timelineItem)
                                    <x-organomania.anniversary-rows :entity="$timelineItem" :$year>
                                        <x-slot:entity-link>
                                            <x-organomania.organ-builder-link :organBuilder="$timelineItem->organBuilder" :name="$timelineItem->name" showActivePeriod :activePeriod="$timelineItem->active_period" />
                                        </x-slot>
                                    </x-organomania.anniversary-rows>
                                @endforeach
                            </table>

                            <div class="mb-4">
                                @php $ids = $anniversaries['timelineItems']->pluck('organBuilder.id')->unique()->toArray()  @endphp
                                <a class="btn btn-sm btn-outline-secondary mt-1 me-1" href="{{ route('organ-builders.index', ['filterId' => $ids, 'viewType' => 'map']) }}">
                                    <i class="bi bi-person-circle"></i>
                                    {{ __('Zobrazit vše') }}
                                    <span class="badge text-bg-secondary rounded-pill">{{ count($ids) }}</span>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4">
        {{ __('Viz též') }}:
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('organ-builders.list-by-age') }}" wire:navigate>
            {{ __('Varhanáři podle věku dožití') }}
        </a>
    </div>
</div>
