@props(['disposition', 'doc' => false])

@php
    use App\Models\DispositionRegister;
    use App\Enums\RegisterCategory;

    $keyboardStartNumbers = $disposition->getKeyboardStartNumbers();
@endphp

<!DOCTYPE html>
<html>
    <head>
        <title>{{ $disposition->name }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            body {
                font-family: DejaVu Sans;
            }
            li.keyboard::marker {
                font-weight: bold;
            }
            h2 {
                margin-top: 0;
                font-weight: normal;
            }
            .keyboards {
                margin-top: 1em;
                width: calc(100% + 40px);
            }
            @if (!$doc)
                .chunk {
                    margin-bottom: 1em;
                }
            @endif
            .keyboard-container {
                display: inline-block;
                vertical-align: top;
                width: calc(50% - 1em);
            }
            .keyboard {
                padding-right: 4em;
                min-width: 16.5em;
            }
            .registers {
                margin-top: 0;
                margin-bottom: 0;
                padding-left: 0;
            }
            .register {
                position: relative;
            }
            .register-pitch {
                position: absolute;
                right: 0;
            }
        </style>
    </head>
    <body @class(['doc' => $doc])>
        <div>
            <h2>{{ $disposition->name }}</h2>

            <ol class="disposition keyboards" type="I">
                @foreach ($disposition->keyboards->chunk(2) as $chunk)
                    <div class="chunk">
                        @foreach ($chunk as $keyboard)
                            <div class="keyboard-container">
                                <li class="keyboard" value="{{ $keyboard->order }}" @style(['list-style-type: none' => $keyboard->pedal])>
                                    <strong>{{ $keyboard->name }}</strong>
                                    @if ($keyboard->dispositionRegisters->isNotEmpty())
                                        <ol class="registers" start="{{ $keyboardStartNumbers[$keyboard->id] }}" @style(['list-style-type: none' => !$disposition->numbering])>
                                            @foreach ($keyboard->dispositionRegisters as $register)
                                                <li class="register disposition-item">
                                                    <span @class(['coupler' => $register->coupler])>
                                                        {{ $register?->registerName?->name ?? $register->name }}
                                                        @isset($register->multiplier)
                                                            {{ DispositionRegister::formatMultiplier($register->multiplier) }}
                                                        @endisset
                                                    </span>

                                                    <span class="register-pitch">
                                                        @if ($register->register?->registerCategory === RegisterCategory::Reed)
                                                            &bull;
                                                        @endif
                                                        @isset($register->pitch)
                                                            {{ $register->pitch->getLabel($disposition->language) }}
                                                        @endisset
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ol>
                                    @endif
                                </li>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </ol>
            
            @isset($disposition->appendix)
                @foreach (explode("\n", $disposition->appendix) as $line)
                    <div>{{ $line }}</div>
                @endforeach
            @endisset
        </div>
    </body>
</html> 
