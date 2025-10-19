<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\Attributes\Url;
use Jfcherng\Diff\Differ;
use Jfcherng\Diff\DiffHelper;
use App\Models\Disposition;
use App\Helpers;

new #[Layout('layouts.app-bootstrap')] class extends Component {
    
    #[Locked]
    #[Url(history: true)]
    public ?int $dispositionId1 = null;
    #[Locked]
    #[Url(history: true)]
    public ?int $dispositionId2 = null;

    public $previousUrl;

    #[Computed]
    public function disposition1()
    {
        if (isset($this->dispositionId1)) return $this->getDisposition($this->dispositionId1);
    }

    #[Computed]
    public function disposition2()
    {
        if (isset($this->dispositionId2)) return $this->getDisposition($this->dispositionId2);
    }

    #[Computed]
    public function exampleDiffs()
    {
        $diffs = [
            [42, 43],       // Brno, Petrov
            [74, 75],       // Dub
            [9, 10],        // Olomouc, PMS
            [16, 17],       // Olomouc, Hejčín
            [3, 4],         // Olomouc, sv. Mořic
            [72, 73],       // Praha, sv. Tomáš
        ];
        return collect($diffs)->filter(
            fn($diff) => $diff != [$this->dispositionId1, $this->dispositionId2]
        )->toArray();
    }

    public function setDiff($dispositionId1, $dispositionId2)
    {
        $this->dispositionId1 = $dispositionId1;
        $this->dispositionId2 = $dispositionId2;
    }

    public function mount()
    {
        $this->previousUrl = request()->headers->get('referer');
    }

    public function rendering(View $view): void
    {
        $title = __('Porovnání dispozic varhan');
        $view->title($title);
    }

    public function rendered()
    {
        $this->dispatch('bootstrap-rendered');
        $this->dispatch('select2-rendered');
        $this->dispatch('select2-sync-needed', componentName: 'pages.disposition-diff');
    }

    #[Computed]
    public function dispositions()
    {
        return Disposition::query()->orderBy('name')->get();
    }

    private function getDisposition($id)
    {
        return Disposition::withCount('realDispositionRegisters')->findOrFail($id);
    }

    private function getDispositionForDiff(Disposition $disposition)
    {
        $text = str($disposition->toPlaintext(numbering: false));

        // výjimky pro přehlednější diff
        switch ($disposition->id) {
            // Olomouc, PMS
            case 9:
                $text = $text->replaceLast("Superoctava 2'", "\nSuperoctava 2'");
                break;
                
            // Olomouc, sv. Mořic
            case 3:
                $text = $text->replaceLast("Pedal", "Pedal (Altes Werk)");
                break;
                
            case 42:
                $text = $text->replaceFirst("Bombardon 16'", "\nBombardon 16'");
                $text = $text->replaceFirst("Fleta oktavová 4'", "\nFleta oktavová 4'");
                $text = $text->replaceFirst("Doublet 2 2/3'", "\nDoublet 2 2/3'");
                $text = $text->replaceFirst("Mixtura 5x 2 2/3'", "\nMixtura 5x 2 2/3'");
                $text = $text->replaceFirst("Cornet 3x 5 1/3'", "\nCornet 3x 5 1/3'");

                $text = $text->replaceFirst("Tercfleta 1 3/5'", "Tercfléta 1 3/5'");
                $text = $text->replaceFirst("Cornet 3x 5 1/2'", "Cornet 3x 5 1/3'");
                break;
                
            // Praha, sv. Tomáš
            case 72:
                $text = $text->replaceFirst("Mixtura 4× 2 2/3'", "\n\nMixtura 4× 2 2/3'");
                break;
                
            // Dub
            case 74:
                $text = $text->replaceFirst("Principal 8'", "\nPrincipál 8'");
                $text = $text->replaceFirst("Octav 4'", "Oktáva 4'");
                $text = $text->replaceFirst("Spitzflöte 8'", "Flétna špičatá 8'");
                $text = $text->replaceFirst("Spitzflöte 4'", "Flétna špičatá 4'");
                $text = $text->replaceFirst("Quinte 2 2/3'", "Quinta 2 2/3'");
                $text = $text->replaceFirst("Superoctav 2'", "Superoktava 2'");
                $text = $text->replaceFirst("Mixtur 2'", "Mixtura 2'");
                $text = $text->replaceFirst("Octavbas 8'", "Oktavbas 8'");
                $text = $text->replaceFirst("Bordunbas 16'", "Bordonbass 16'");
                $text = $text->replaceFirst("Subbas 16'", "Subbass 16'");
                //$text = $text->replaceFirst("Flauta 8'", "Flétna 8'");
                $text = $text->replaceFirst("Flauta major 8'", "Flétna major 8'");
                $text = $text->replaceFirst("Flauta minor 4'", "Flétna minor 4'");
                $text = $text->replaceFirst("Principal 4'", "\nPrincipál 4'");
                $text = $text->replaceFirst("Waldflöte 4'", "Flétna lesní 4'");
                $text = $text->replaceFirst("Mixtur 1 1/3'", "Mixtura 1 1/3'");
                $text = $text->replaceFirst("Superoktav 2'", "Superoktava 2'");
                break;
            
            case 75:
                $text = $text->replaceFirst("Cornett 4×", "\nCornett 4× (sloučení Sexty a Cimbalu)");
                $text = $text->replaceFirst("Cellobas 8'", "\nCellobas 8'");
                //$text = $text->replaceFirst("Flétna 8'", "Flétna (major?) 8'");
                break;
        }

        return $text->toString();
    }

    #[Computed]
    public function diff()
    {
        if (isset($this->disposition1) && isset($this->disposition2)) {
            $rendererName = 'SideBySide';
            $differOptions = [
                'ignoreCase' => true,
                'ignoreLineEnding' => true,
                'ignoreWhitespace' => true,
                'lengthLimit' => 10_000,
                'context' => Differ::CONTEXT_ALL,
            ];
            if (in_array($this->disposition1->id, [740])) $detailLevel = 'char';
            else $detailLevel = 'word';
            $rendererOptions = [
                // diff po znacích v některých případech nefunguje (zvýrazní se řádek, ale ne jednotlivé znaky)
                //  - zřejmě chyba algoritmu, při jiném uspořádání řádků funguje
                'detailLevel' => $detailLevel,
                'lineNumbers' => false,
                'language' => app()->getLocale() === 'en' ? 'eng' : 'cze',
                'showHeader' => false,
                'wrapperClasses' => ['diff-wrapper'],
                'resultForIdenticals' => '<identical>',
            ];
            $result = DiffHelper::calculate(
                $this->getDispositionForDiff($this->disposition1),
                $this->getDispositionForDiff($this->disposition2),
                $rendererName, $differOptions, $rendererOptions
            );
            // ztučnění názvu manuálu
            $result = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $result);
            return $result;
        }
    }

    #[Computed]
    public function diffCounts()
    {
        if (isset($this->disposition1) && isset($this->disposition2)) {
            return [
                'keyboards' => $this->disposition2->keyboards->count() - $this->disposition1->keyboards->count(),
                'registers' => $this->disposition2->real_disposition_registers_count - $this->disposition1->real_disposition_registers_count,
            ];
        }
    }

    private function formatDiffCount($diff)
    {
        if ($diff === 0) return __('stejný');
        if ($diff > 0) return "+$diff";
        return (string)$diff;
    }

    public function swap()
    {
        if (isset($this->dispositionId1) || isset($this->dispositionId2)) {
            Helpers::swap($this->dispositionId1, $this->dispositionId2);
        }
    }

}; ?>

<div class="container disposition-diff">
    
    @push('meta')
        <meta name="description" content="{{ __('Porovnejte rejstříky obsažené v dispozicích vybraných varhan. Zjistěte, jaké změny rejstříkové dispozice nastaly po provedených přestavbách.') }}">
    @endpush
    
    <h3>{{ __('Porovnání dispozic') }}</h3>
    
    {{-- bez atributu wire:replace se po swapu nezobrazí v selectech hodnoty (souvisí s excludeDispositionId) --}}
    <div class="row g-2 justify-content-between" wire:replace>
        <div class="col-10 col-lg-5">
            <label class="form-label" for="dispositionId1">{{ __('Dispozice') }} 1</label>
            <div class="hstack">
                <x-organomania.selects.disposition-select
                    :dispositions="$this->dispositions"
                    model="dispositionId1"
                    :excludeDispositionId="$this->dispositionId2"
                />
                @isset($dispositionId1)
                    <a class="btn btn-sm btn-primary ms-1" href="{{ route('dispositions.show', $this->disposition1?->slug) }}" data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit dispozici') }}" wire:navigate>
                        <i class="bi-eye"></i>
                    </a>
                @endisset
            </div>
        </div>
        <div class="col-auto align-content-end text-center">
            <button class="btn btn-sm btn-primary position-relative" wire:click="swap" data-bs-toggle="tooltip" data-bs-title="{{ __('Prohodit dispozice') }}" style="bottom: 11px">
                <i class="bi-arrow-left-right"></i>
            </button>
        </div>
        <div class="col-10 col-lg-5">
            <label class="form-label" for="dispositionId1">{{ __('Dispozice') }} 2</label>
            <div class="hstack">
                <x-organomania.selects.disposition-select
                    :dispositions="$this->dispositions"
                    model="dispositionId2"
                    :excludeDispositionId="$this->dispositionId1"
                />
                @isset($dispositionId2)
                    <a class="btn btn-sm btn-primary ms-1" href="{{ route('dispositions.show', $this->disposition2?->slug) }}" data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit dispozici') }}" wire:navigate>
                        <i class="bi-eye"></i>
                    </a>
                @endisset
            </div>
        </div>
    </div>
    
    <div class="position-relative mt-4" wire:loading.class="opacity-25">
        <div wire:loading class="position-absolute text-center w-100">
            <x-organomania.spinner />
        </div>
        @isset($this->diff)
            @php
                if ($this->disposition1->isEmpty() || $this->disposition2->isEmpty()) {
                    $message = __('Dispozice :number neobsahuje žádné manuály a rejstříky.', [
                        'number' => $this->disposition1->isEmpty() ? 1 : 2
                    ]);
                }
                elseif ($this->diff === '<identical>') {
                    $message = __('Dispozice jsou shodné.');
                }
                else $message = null
            @endphp
        
            @isset ($message)
                <div class="text-center text-body-secondary">
                    {{ $message }}
                </div>
            @else
                <div>{!! $this->diff !!}</div>
                
                <div class="mt-3 text-center text-body-secondary">
                    @if ($this->diffCounts['keyboards'] !== 0)
                        <div>{{ __('Rozdíl počtu manuálů') }}: {{ $this->formatDiffCount($this->diffCounts['keyboards']) }}</div>
                    @endif
                    <div>{{ __('Rozdíl počtu znějících rejstříků') }}: {{ $this->formatDiffCount($this->diffCounts['registers']) }}</div>
                </div>
            @endisset
        @else
            <div class="text-center text-body-secondary">
                {{ __('Zvolte dispozice pro porovnání.') }}
            </div>
        @endisset
    </div>
    
    @if (!empty($this->exampleDiffs))
        <div class="text-body-secondary" style="font-size: 85%;">
            <br />
            {{ isset($this->diff) ? __('Další příklady porovnání') : __('Příklady porovnání') }}
            
            <ul>
            @foreach ($this->exampleDiffs as [$dispositionId1, $dispositionId2])
                <li>
                    <a href="#" class="link-primary text-decoration-none" wire:click.prevent="setDiff({{ $dispositionId1 }}, {{ $dispositionId2 }})">
                        {{ Disposition::find($dispositionId1)->name }}
                        &LeftRightArrow;
                        <br />
                        {{ Disposition::find($dispositionId2)->name }}
                    </a>
                </li>
            @endforeach
            </ul>
        </div>
    @endif
    
    @push('styles')
        <style>
            {!! DiffHelper::getStyleSheet() !!}
        </style>
    @endpush
    
    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" wire:navigate href="{{ $this->previousUrl ?? route('dispositions.index') }}"><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>&nbsp;
    </div>
</div>
