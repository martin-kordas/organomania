# Prirucka pro AI agenty

## Ucel dokumentu
- Poskytnout strojove citelne i lidsky srozumitelne shrnuti projektu Organomania.
- Zajistit, aby automatizovani agenti rozumeli domene, architekture a akceptovanym pracovnim postupum.
- Slouzit jako vychozi referencni bod pri diagnostice chyb, navrhu uprav i psani testu.

## Rychle seznameni
- **Projekt**: webova aplikace prezentujici vyznamne varhany, varhanare, festivaly a dispozice (Laravel 11, Livewire 3, Bootstrap 5.3).
- **Repo**: logika v `app/`, data v `data/`, sablony v `resources/views`.
- **Cilovi uzivatele**: hudebni nadsenci, studenti, spravci obsahu.
- **Klicove scenare**: prohlizeni a filtrovani varhan, PDF dispozice, import dat, registrace s podporou AI, sber statistik.

## Backendova architektura
- **Routovani** (`routes/web.php`): Volt Livewire stranky + klasicke kontrolery pro exporty a presmerovani.
- **Kontrolery**: `OrganController` vyrabi PDF dispozice (DomPDF) a resi signed odkazy.
- **Livewire formulare** (`app/Livewire/Forms`): obsluha editace varhan, dispozic, registrac; `DispositionForm` uklada klaviatury a rejstriky v transakci.
- **Sluzby** (`app/Services`):
  - `AI/*` volaji OpenAI klienta pro popis dispozic, navrhy registraci, OCR.
  - `VarhanyNetService` scrapuje externi web a mapuje kategorie.
  - Podporne sluzby pro Markdown, geokodovani, runtime statistiky atd.
- **Helpers** (`App\Helpers`): formatovani dat, prace s retezci, diakritikou a cache navstev.
- **Traits/Observvery**: `OwnedEntity` + `OwnedEntityScope` (vlastnictvi zaznamu), `OrganObserver` cisti relace pri mazani.

## Domenovy model
- `Organ`: lokalita, stavitel, prestavby, dispozice, registrace, statistiky navstev, likes, vztahy na festivaly a souteze.
- `Disposition`, `Keyboard`, `DispositionRegister`: struktura dispozic vcetne poradi a parametru rejstriku.
- `OrganBuilder`, `OrganBuilderTimelineItem`: historie varhanaru a dilen.
- `Registration`, `RegistrationSet`: ulozene registrace pro konkretni skladby.
- `Enums` (napr. `Region`, `OrganCategory`, `Pitch`): konzistentni identifikatory.
- `Like`, `Stats`, `VarhanyNetOrgan*`: gamifikace, analytika, historie importu.

## Datove toky
- **Importy** (`php artisan app:import-data {--seed}`): zpracovani CSV/Markdown, mapovani ID, doplneni kategorii a vztahu na festivaly/souteze.
- **Scraping** (`VarhanyNetService`): stahuje HTML, extrahuje historii stavby, uklada serializovana data pro audit.
- **Statistiky** (`php artisan app:collect-stats {--db} {--mailto=*}`): pocita sumary, volitelne uklada do DB a posila e-maily `StatsCollected`.
- **AI requesty** (`App\Services\AI`): jednotne prompty, normalizovane dispozice, logovani requestu.
- **PDF exporty**: `OrganController@exportDispositionAsPdf` rendruje Blade sablonu do PDF.

## Uzivatelske rozhrani
- **Livewire/Volt** (`resources/views/livewire/pages`): seznamy a detaily varhan, stavitelu, festivalu, soutezi, dispozic, registraci, kvizu.
- **Blade komponenty** (`resources/views/components/organomania`): mapy, tabulky, PDF fragmenty.
- **Layout** (`resources/views/livewire/layout/app-bootstrap.blade.php`): Bootstrap + Livewire skripty.
- **Prava**: `OwnedEntityPolicy` omezuje pristup k soukromym zaznamum, `OrganPolicy` ma specialni pravidla pro pisne.

## AI sluzby detailne
- `DispositionAI`: normalizuje radky, pripadne doplni cislovani rejstriku, generuje kontext o nastroji (`getOrganInfo`).
- `DescribeDispositionAI`: vytvori popis dispozice v aktivnim locale, zachovava Markdown a loguje kazdy dotaz.
- `SuggestRegistrationAI`: vybere rejstriky (cisla v hranatych zavorkach), volitelne generuje komentar bez cisel a mapuje je na radky dispozice pro UI.
- `DispositionOcr`: vyuziva OpenAI pro prevod obrazove dispozice (pouziti vyhledat v `app/Services/AI`).

## CLI nastroje
- `app:import-data` ? plny import + volitelne znovu seeduje zaklad.
- `app:collect-stats` ? vypocet statistik, ulozeni do DB, odeslani e-mailu.
- Dalsi: `app:geocode`, `app:scrape-varhany-net`, `app:update-organists`, `app:collect-stats --schedule` (bez potvrzovaciho dotazu).

## Konfigurace a prostredi
- Composer post-skripty vytvareji `.env`, spousti migrace, aktualizuji jazyky.
- `config/custom.php` ovlada bannery, simulace nacitani, viditelnost dulezitosti.
- `laravel/scout` definuje fulltext nad `Organ`, `OrganBuilder`, `Disposition`.
- Build stack: Vite, Tailwind, NPM skripty (`npm run dev`, `npm run build`).
- Monitorovani: `opcodesio/log-viewer` pro prohlizeni logu v UI.

## Doporuceny workflow pro agenty
- **Analyza**: projit relevantni soubory (README, modely, sluzby) a pochopit dopad.
- **Implementace**: vyuzivat helpery, respektovat vlastnictvi entit, pri ukladani dispozic drzet transakce a generovani poradi.
- **Testovani**: `php artisan test`, pripadne `./vendor/bin/phpstan analyse`; pri uprave assetu spustit `npm run build`.
- **Overeni importu**: po zmenach v CSV logice spustit `php artisan app:import-data --seed` nad bezpecnou kopii.
- **Vystup**: v PR shrnout dopad, dopsat kroky pro manualni kontrolu, aktualizovat dokumentaci podle potreby.

## Checklist pred merge
- [ ] Nova dependency je zdokladovana v `composer.json` / `package.json`.
- [ ] Existuje test nebo popsan? manualni postup overeni.
- [ ] Livewire formulare pokryvaji edge-case (duplicitni rejstriky, validace ID).
- [ ] AI sluzby loguji chyby a maji fallback, ktery neblokuje UI.
- [ ] Dokumentace (README, prirucka) je aktualni.

## Slovnicek pojmu
- **Dispozice**: textovy zapis rejstriku, manualu a pedalu konkretniho nastroje.
- **Registrace**: kombinace rejstriku pro urceny repertoar nebo skladbu.
- **Varhany.net**: externi databaze, ze ktere se importuji historie varhan a varhanaru.
- **Owned entity**: zaznam s pripadnym `user_id`, verejne zaznamy ho nemaji.
- **Volt stranka**: Livewire komponenta registrovana pres `Livewire\\Volt\\Volt::route` a reprezentovana Blade sablonou v `resources/views/livewire/pages`.
