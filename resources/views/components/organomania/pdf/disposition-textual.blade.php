@props(['organ', 'disposition'])

<!DOCTYPE html>
<html>
    <head>
        <title>{{ $organ->municipality }}, {{ $organ->place }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            body {
                font-family: DejaVu Sans;
            }
            h2, h3 {
                margin-top: 0;
                font-weight: normal;
            }
            h2 {
              margin-bottom: 0;
            }
            .disposition {
                white-space: pre-line;
                display: inline-block;
                width: 450px;
                position: relative;
            }
            .register-pitch {
                position: absolute;
                right: 0;
            }
        </style>
    </head>
    <body>
        <div>
            <h2>{{ __('organ_disposition_1') }}</h2>
            <h3>{{ $organ->municipality }}, {{ $organ->place }}</h3>
            
            {{-- TODO: chybí sloupcový layout --}}
            {{--   - nelze použít columns-count, protože dompdf nepodporuje CSS sloupce --}}
            {{--   - lze řešit stejně jako v PDF exportu interaktivní dispozice - rozělit dispozici po klaviaturách a vypsat každou klaviaturu v samostatném inline-block divu --}}
            {{--   - textová dispozice ale obsahuje variabilnější data než interaktivní dispozice, takže vyladění by bylo těžší --}}
            <div class="disposition">{!! $disposition !!}</div>
        </div>
    </body>
</html> 
