# P??ru?ka pro AI agenty

## ??el dokumentu
- Poskytnout strojov? ?iteln? i lidsky srozumiteln? shrnut? projektu Organomania.
- Zajistit, aby automatizovan? agenti rozum?li dom?n?, architektu?e a akceptovan?m pracovn?m postup?m.
- Slou?it jako v?choz? referen?n? bod p?i diagnostice chyb, n?vrhu ?prav i psan? test?.

## Rychl? sezn?men?
- **Projekt**: webov? aplikace prezentuj?c? v?znamn? varhany, varhan??e, festivaly a dispozice (Laravel 11, Livewire 3, Bootstrap 5.3).
- **Repo**: hlavn? logika v `app/`, datov? podklady v `data/`, ?ablony v `resources/views`.
- **C?lov? u?ivatel?**: hudebn? nad?enci, studenti, spr?vci obsahu.
- **Kl??ov? sc?n??e**: prohl??en? a filtrov?n? varhan, generov?n? PDF dispozic, import dat, n?vrhy registrac? pomoc? AI, sb?r statistik.

## Backendov? architektura
- **Routov?n?** (`routes/web.php`): kombinace Volt rout pro Livewire str?nky a klasick?ch kontroler? pro exporty, p?esm?rov?n? a statick? pohledy.
- **Kontrolery**: nap?. `OrganController` ?e?? export dispozic do PDF (barryvdh/laravel-dompdf) a speci?ln? p?esm?rov?n? na signed routy.
- **Livewire formul??e** (`app/Livewire/Forms`): stavov? objekty pro editaci varhan, dispozic ?i registrac?. Nap?. `DispositionForm` orchestruje transak?n? ukl?d?n? klaviatur a rejst??k?.
- **Slu?by** (`app/Services`):
  - `AI/*` pro pr?ci s OpenAI (popis dispozice, n?vrh registrace, OCR).
  - `VarhanyNetService` pro scraping dat z extern?ho webu a jejich normalizaci.
  - `MarkdownConvertorService`, `RuntimeStatsService` a dal?? podp?rn? utility.
- **Pomocn? funkce** (`App\Helpers`): unifikovan? form?tov?n? (??sla, datumy, ??msk? ??slice), pr?ce s n?zvy a highlightov?n? textu.
- **Obsersvery a Traits**: `OwnedEntity` a `OwnedEntityScope` implementuj? ?vlastnictv?? z?znam?, `OrganObserver` ?i `DispositionObserver` synchronizuj? relace p?i maz?n?.

## Dom?nov? model
- `Organ`: z?kladn? entita s lokalitou, stavitelem, p?estavbami, dispozicemi, statistikami n?v?t?vnosti a relacemi na sout??e/festivaly.
- `Disposition`, `Keyboard`, `DispositionRegister`: reprezentuj? strukturu dispozice varhan v?etn? po?ad?, jazyk? a parametr? rejst??k?.
- `OrganBuilder`, `OrganBuilderTimelineItem`: data o stavitel?ch, d?ln?ch a p?ehledy reprezentativn?ch n?stroj?.
- `Registration`, `RegistrationSet`: ukl?daj? doporu?en? rejst??k? pro konkr?tn? skladby a dispozice.
- `Enums` (`app/Enums`): `Region`, `OrganCategory`, `Pitch` aj. normalizuj? konstanty v DB a UI.
- `Like`, `Stats`, `VarhanyNetOrgan*`: gamifikace, analytika a historie import?.

## Datov? toky a integrace
- **CSV/Markdown importy** (`app:import-data`): ?tou `data/organs.csv`, `organBuilders.csv`, Markdown dispozice atd., mapuj? p?vodn? ID na nov?, ukl?daj? vazby, synchronizuj? festivaly a sout??e.
- **Scraping** (`VarhanyNetService`): stahuje HTML str?nky varhany.net, extrahuje historii stavby, kategoriza?n? k?dy a ukl?d? serializovan? data pro audit.
- **Statistiky** (`app:collect-stats`): agreguj? po?ty u?ivatel?, zhl?dnut? a lajk?, voliteln? ukl?daj? do DB a rozes?laj? e-mail `StatsCollected`.
- **AI requesty** (`App\Services\AI`): standardizovan? prompty a form?tov?n? dispozic; vyu??vaj? `OpenAI\Contracts\ClientContract` (viz `composer.json`).
- **PDF exporty**: `OrganController@exportDispositionAsPdf` rendruje `components.organomania.pdf.disposition-textual` do PDF p?es DomPDF.

## U?ivatelsk? rozhran?
- **Volt / Livewire str?nky** (`resources/views/livewire/pages`): poskytuj? seznamy, detailn? pohledy a editory varhan, stavitel?, festival?, stop, registrac? i kv?zu.
- **Blade komponenty** (`resources/views/components/organomania`): mapy, seznamy, karty, PDF ?ablony a specializovan? UI prvky.
- **Layout** (`resources/views/livewire/layout/app-bootstrap.blade.php`): kombinuje Bootstrap, Livewire a vlastn? skripty.
- **Autentizace**: guardy a policy `OwnedEntityPolicy` omezuj? p??stup k priv?tn?m entit?m, `OrganPolicy` roz?i?uje logiku pro p?sn?.

## AI slu?by podrobn?
- `DispositionAI`
  - Normalizuje ??dkov?n?, voliteln? ??sluje rejst??ky, ukl?d? ?plain? verzi dispozice.
  - `getOrganInfo()` sestavuje textov? kontext (lokace, stavitel, p?estavby, styl) pro prompt.
- `DescribeDispositionAI`
  - Vygeneruje charakteristiku dispozice v aktu?ln?m locale, zachov?v? Markdown bez nadm?rn?ho form?tov?n?.
  - Loguje ka?d? request (`Log::info`).
- `SuggestRegistrationAI`
  - Prvn? dotaz vyb?r? rejst??ky a vrac? ??sla v hranat?ch z?vork?ch.
  - Druh? (voliteln?) dotaz generuje doporu?en? bez ??sel rejst??k? a s minim?ln?m form?tov?n?m.
  - `processResponse()` mapuje ??sla rejst??k? na indexy ??dk? dispozice pro zv?razn?n? ve frontend komponent?.
- `DispositionOcr` (nen? v dokumentu rozebr?na) pravd?podobn? pracuje s OpenAI Vision API pro p?evod nahr?van?ch dispozic.

## CLI n?stroje
- `php artisan app:import-data {--seed}`: kompletn? import dat, voliteln? znovu spust? seedery.
- `php artisan app:collect-stats {--db} {--mailto=*} {--schedule}`: sb?r statistik, ukl?d?n? do DB, e-mail notifikace, automatick? potvrzen? ve schedule re?imu.
- Dal?? p??kazy: `app:geocode`, `app:scrape-varhany-net`, `app:update-organists` atd. (viz `app/Console/Commands`).

## Konfigurace a prost?ed?
- `.env` generuje `post-root-package-install` script v Composeru.
- `config/custom.php` ??d? bannery, simulaci na??t?n? a viditelnost d?le?itosti varhan.
- `laravel/scout` nastavuje fulltext pro `Organ`, `OrganBuilder` a `Disposition`.
- Vite + Tailwind slou?? pro asset pipeline (`vite.config.js`, `resources/css`, `resources/js`).
- Log viewer (opcodesio/log-viewer) umo??uje inspekci log? v UI.

## Doporu?en? pracovn? postup pro AI agenty
- **Anal?za po?adavku**
  - P?e?ti `README.md` a relevantn? sekce zdroj?.
  - Ur?i dot?en? entity a slu?by (nap?. zm?ny dispozic ? `DispositionForm`, exporty ? `OrganController`).
- **Implementace**
  - Vyu?ij existuj?c? helpery, enumy a trait `OwnedEntity`.
  - P?i pr?ci s dispozicemi v?dy respektuj ukl?d?n? v transakci a generov?n? po?ad? (`fillEntitiesWithOrder`).
  - P?i roz?i?ov?n? AI prompt? zachovej konzistentn? styl a logov?n?.
- **Testov?n?**
  - Spus? `php artisan test` a podle pot?eby `./vendor/bin/phpstan analyse`.
  - Pro ?pravy asset? vyu?ij `npm run build`/`npm run dev`.
  - Po importn?ch zm?n?ch prove? smoke test `php artisan app:import-data --seed` na izolovan?ch datech.
- **Dokumentace a v?stup**
  - Shr? dopad zm?n (modely, migrace, UI) a navrhni ov??ovac? kroky pro lidsk?ho reviewera.

## Referen?n? checklist p?ed merge
- [ ] V?echny nov? dependency jsou p?id?ny do `composer.json` / `package.json` s vysv?tlen?m.
- [ ] Existuj? testy nebo alespo? manu?ln? popis ov??en?.
- [ ] Livewire formul??e maj? o?et?en? edge-case sc?n??e (duplicitn? rejst??ky, validace ID).
- [ ] AI slu?by loguj? chyby a maj? fallback, kter? neblokuje UI.
- [ ] Dokumentace (README nebo tato p??ru?ka) je v p??pad? pot?eby aktualizov?na.

## Slovn??ek pojm?
- **Dispozice**: textov? z?pis rejst??k?, manu?l? a ped?l? konkr?tn?ho varhann?ho n?stroje.
- **Registrace**: kombinace rejst??k? doporu?en? pro konkr?tn? skladbu nebo styl.
- **Varhany.net**: extern? datab?ze varhan, ze kter? aplikace p?eb?r? historick? data.
- **Owned entity**: z?znam, kter? m??e b?t vlastn?n konkr?tn?m u?ivatelem; ve?ejn? z?znamy maj? `user_id == null`.
- **Volt str?nka**: Livewire komponenta definovan? v Blade souboru, registrovan? p?es `Livewire\Volt\Volt::route`.

