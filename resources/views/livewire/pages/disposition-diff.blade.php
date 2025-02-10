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
            [3, 4],         // Olomouc, sv. Mořic
            [9, 10],        // Olomouc, PMS
            [16, 17],       // Olomouc, Hejčín
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
            $rendererOptions = [
                // diff po znacích v některých případech nefunguje (zvýrazní se řádek, ale ne jednotlivé znaky)
                //  - zřejmě chyba algoritmu, při jiném uspořádání řádků funguje
                'detailLevel' => 'char',
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
            $result1 = DiffHelper::calculate(
                <<<EOL
**I. manuál**
Principal 16'
Bourdun 16'
Principal 8'
Fleta dvojitá 8'
Kryt hrubý 8'
Gamba 8'
Roh kamzíkový 8'
Salicional 8'
Oktava 4'
Fleta dutá 4'
Fleta rourová 4'
Fugara 4'
Mixtura 6x 5 1/3'
Cornet 4x 4'
Quinta šumivá 2 2/3'
Cimbal 3x 2'

Bombardon 16'
Trompeta 8'
Clairon 4'

**II. manuál**
Bourdun 16'
Gamba 16'
Principal 8'
Fleta harmonická 8'
Fleta jemná 8'
Kryt 8'
Fugara 8'
Viola d'amour 8'
Dolce 8'

Fleta oktavová 4'
Oktava 4'

Doublet 2 2/3'

Mixtura 5x 2 2/3'
Trompeta 8'
Clarinet 8'

**III. manuál**
Kryt jemný 16'
Salicet 16'
Principal fletový 8'
Quintadena 8'
Kryt líbezný 8'
Fleta líbezná 8'
Harmonika 8'
Vox coelestis 8'
Aeolina 8'
Oktava 4'
Fleta příčná 4'
Violino 4'
Flageolet 2 2/3'
Pikola 2'
Tercfléta 1 3/5'
Progresse 4x 2 2/3'
Oboe 8'

**Pedál**
Podstav 32'
Principalbas 16'
Violonbas 16'
Subbas 16'
Kryt tichý 16'
Dolcebas 16'
Quintbas 10 2/3'
Oktavbas 8'
Bourdunbas 8'
Cello 8'

Cornet 3x 5 1/3'
Pozoun 16'
Trompeta 8'
Clairon 4'
EOL,
                <<<EOL
**I. manuál**
Principal 16'
Bourdun 16'
Principal 8'
Flétna dvojitá 8'
Kryt hrubý 8'
Gamba 8'
Roh kamzičí 8'
Salicional 8'
Kvinta 5 1/3'
Oktava 4'
Flétna dutá 4'
Flétna rourková 4'
Fugara 4'
Oktava špičatá 2'
Mixtura 6x 5 1/3'
Cornet 3-5x 2 2/3'
Kvinta šumivá 2x 2 2/3'
Cimbal 2-3x 2'
Akuta 4-6x 1 1/3'
Bombard 16'
Trompeta 8'
Clairon 4'

**II. manuál**
Bourdun 16'
Gamba 16'
Principal 8'
Flétna harmonická 8'
Flétna jemná 8'
Kryt 8'
Fugara 8'
Viola d´amour 8'
Dolce 8'
Unda maris 8'
Flétna oktávová 4'
Oktava 4'
Dolkan 2'
Doublet 2x 2 2/3'
Tercseptima 1-2x 1 3/5'+1 1/7'
Mixtura 3x 1 1/3'
Trompeta 8'
Clarinet 8'

**III. manuál**
Kryt jemný 16'
Salicet 16'
Principal flétnový 8'
Kvintadena 8'
Kryt líbezný 8'
Flétna jemná 8'
Harmonika 8'
Vox coelestis 8'
Aeolina 8'
Oktava 4'
Flétna příčná 4'
Violino 4'
Flageolet 2 2/3'
Picola 2'
Tercfléta 1 3/5'
Progresse 3-4x 2 2/3'
Scharf 3-4x 1 1/3'
Oboe 8'

**Pedál**
Grand Bourdon 32'
Principalbas 16'
Violon 16'
Subbas 16'
Kryt tichý 16'
Dolcebas 16'
Kvintbas 10 2/3'
Oktavbas 8'
Bourdun 8'
Violoncello 8'
Oktavbas 4'
Cornet 3x 5 1/3'
Kontrafagot 32'
Pozoun 16'
Trompeta 8'
Clairon 4'
EOL,
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
            <button class="btn btn-sm btn-primary" wire:click="swap" data-bs-toggle="tooltip" data-bs-title="{{ __('Prohodit dispozice') }}">
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
        <div class="text-center text-body-secondary" style="font-size: 85%;">
            <br />
            {{ isset($this->diff) ? __('Další příklady porovnání') : __('Příklady porovnání') }}
            @foreach ($this->exampleDiffs as [$dispositionId1, $dispositionId2])
                <div>
                    <a href="#" class="link-primary text-decoration-none" wire:click.prevent="setDiff({{ $dispositionId1 }}, {{ $dispositionId2 }})">
                        {{ Disposition::find($dispositionId1)->name }}
                        &LeftRightArrow;
                        {{ Disposition::find($dispositionId2)->name }}
                    </a>
                </div>
            @endforeach
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
