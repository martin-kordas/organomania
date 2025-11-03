# P??ru?ka pro AI agenty

## ??el dokumentu
- Poskytnout strojov? ?iteln? i lidsky srozumiteln? shrnut? projektu Organomania.
- Zajistit, aby automatizovan? agenti rozum?li dom?n?, architektu?e a akceptovan?m pracovn?m postup?m.
- Slou?it jako v?choz? referen?n? bod p?i diagnostice chyb, n?vrhu ?prav i psan? test?.

## Rychl? sezn?men?
- **Projekt**: webov? aplikace prezentuj?c? v?znamn? varhany, varhan??e, festivaly a dispozice (Laravel 11, Livewire 3, Bootstrap 5.3).
- **Repo**: logika v `app/`, data v `data/`, ?ablony v `resources/views`.
- **C?lov? u?ivatel?**: hudebn? nad?enci, studenti, spr?vci obsahu.
- **Kl??ov? sc?n??e**: prohl??en? a filtrov?n? varhan, PDF dispozice, import dat, registrace s podporou AI, sb?r statistik.

## Backendov? architektura
- **Routov?n?** (`routes/web.php`): Volt Livewire str?nky + klasick? kontrolery pro exporty a p?esm?rov?n?.
- **Kontrolery**: `OrganController` vyr?b? PDF dispozice (DomPDF) a ?e?? signed odkazy.
- **Livewire formul??e** (`app/Livewire/Forms`): obsluha editace varhan, dispozic, registrac?; `DispositionForm` ukl?d? klaviatury a rejst??ky v transakci.
- **Slu?by** (`app/Services`):
  - `AI/*` volaj? OpenAI klienta pro popis dispozic, n?vrhy registrac?, OCR.
  - `VarhanyNetService` scrapuje extern? web a mapuje kategorie.
  - Podp?rn? slu?by pro Markdown, geok?dov?n?, runtime statistiky atd.
- **Helpers** (`App\Helpers`): form?tov?n? dat, pr?ce s ?et?zci, diakritikou a cache n?v?t?v.
- **Traits/Observery**: `OwnedEntity` + `OwnedEntityScope` (vlastnictv? z?znamu), `OrganObserver` ?ist? relace p?i maz?n?.

## Dom?nov? model
- `Organ`: lokalita, stavitel, p?estavby, dispozice, registrace, statistiky n?v?t?v, likes, vztahy na festivaly a sout??e.
- `Disposition`, `Keyboard`, `DispositionRegister`: struktura dispozic v?etn? po?ad? a parametr? rejst??k?.
- `OrganBuilder`, `OrganBuilderTimelineItem`: historie varhan??? a d?len.
- `Registration`, `RegistrationSet`: ulo?en? registrace pro konkr?tn? skladby.
- `Enums` (nap?. `Region`, `OrganCategory`, `Pitch`): konzistentn? identifik?tory.
- `Like`, `Stats`, `VarhanyNetOrgan*`: gamifikace, analytika, historie importu.

## Datov? toky
- **Importy** (`php artisan app:import-data {--seed}`): zpracov?n? CSV/Markdown, mapov?n? ID, dopln?n? kategori? a vztah? na festivaly/sout??e.
- **Scraping** (`VarhanyNetService`): stahuje HTML, extrahuje historii stavby, ukl?d? serializovan? data pro audit.
- **Statistiky** (`php artisan app:collect-stats {--db} {--mailto=*}`): po??t? sum??e, voliteln? ukl?d? do DB a pos?l? e-maily `StatsCollected`.
- **AI requesty** (`App\Services\AI`): jednotn? prompty, normalizovan? dispozice, logov?n? request?.
- **PDF exporty**: `OrganController@exportDispositionAsPdf` rendruje Blade ?ablonu do PDF.

## U?ivatelsk? rozhran?
- **Livewire/Volt** (`resources/views/livewire/pages`): seznamy a detaily varhan, stavitel?, festival?, sout???, dispozic, registrac?, kv?zu.
- **Blade komponenty** (`resources/views/components/organomania`): mapy, tabulky, PDF fragmenty.
- **Layout** (`resources/views/livewire/layout/app-bootstrap.blade.php`): Bootstrap + Livewire skripty.
- **Pr?va**: `OwnedEntityPolicy` omezuje p??stup k soukrom?m z?znam?m, `OrganPolicy` m? speci?ln? pravidla pro p?sn?.

## AI slu?by detailn?
- `DispositionAI`: normalizuje ??dky, p??padn? dopln? ??slov?n? rejst??k?, generuje kontext o n?stroji (`getOrganInfo`).
- `DescribeDispositionAI`: vytvo?? popis dispozice v aktivn?m locale, zachov?v? Markdown a loguje ka?d? dotaz.
- `SuggestRegistrationAI`: vybere rejst??ky (??sla v hranat?ch z?vork?ch), voliteln? generuje koment?? bez ??sel a mapuje je na ??dky dispozice pro UI.
- `DispositionOcr`: vyu??v? OpenAI pro p?evod obrazov? dispozice (pou?it? ve `app/Services/AI`).

## CLI n?stroje
- `app:import-data` ? pln? import + voliteln? znovu seeduje z?klad.
- `app:collect-stats` ? v?po?et statistik, ulo?en? do DB, odesl?n? e-mailu.
- Dal??: `app:geocode`, `app:scrape-varhany-net`, `app:update-organists`, `app:collect-stats --schedule` (bez potvrzovac?ho dotazu).

## Konfigurace a prost?ed?
- Composer post-skripty vytv??ej? `.env`, spou?t? migrace, aktualizuj? jazyky.
- `config/custom.php` ovl?d? bannery, simulace na??t?n?, viditelnost d?le?itosti.
- `laravel/scout` definuje fulltext nad `Organ`, `OrganBuilder`, `Disposition`.
- Build stack: Vite, Tailwind, NPM skripty (`npm run dev`, `npm run build`).
- Monitorov?n?: `opcodesio/log-viewer` pro prohl??en? log? v UI.

## Doporu?en? workflow pro agenty
- **Anal?za**: proj?t relevantn? soubory (README, modely, slu?by) a pochopit dopad.
- **Implementace**: vyu??vat helpery, respektovat vlastnictv? entit, p?i ukl?d?n? dispozic dr?et transakce a generov?n? po?ad?.
- **Testov?n?**: `php artisan test`, p??padn? `./vendor/bin/phpstan analyse`; p?i ?prav? asset? spustit `npm run build`.
- **Ov??en? importu**: po zm?n?ch v CSV logice spustit `php artisan app:import-data --seed` nad bezpe?nou kopi?.
- **V?stup**: v PR shrnout dopad, dopsat kroky pro manu?ln? kontrolu, aktualizovat dokumentaci dle pot?eby.

## Checklist p?ed merge
- [ ] Nov? dependency je zdokumentovan? v `composer.json` / `package.json`.
- [ ] Existuje test nebo popsan? manu?ln? postup ov??en?.
- [ ] Livewire formul??e pokr?vaj? edge-case (duplicitn? rejst??ky, validace ID).
- [ ] AI slu?by loguj? chyby a maj? fallback, kter? neblokuje UI.
- [ ] Dokumentace (README, p??ru?ka) je aktu?ln?.

## Slovn??ek pojm?
- **Dispozice**: textov? z?pis rejst??k?, manu?l? a ped?l? konkr?tn?ho n?stroje.
- **Registrace**: kombinace rejst??k? pro ur?en? reperto?r nebo skladbu.
- **Varhany.net**: extern? datab?ze, ze kter? se importuj? historie varhan a varhan???.
- **Owned entity**: z?znam s p??padn?m `user_id`, ve?ejn? z?znamy ho nemaj?.
- **Volt str?nka**: Livewire komponenta registrovan? p?es `Livewire\Volt\Volt::route` a reprezentovan? Blade ?ablonou v `resources/views/livewire/pages`.
