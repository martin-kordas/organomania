# Příručka pro AI agenty

## Účel dokumentu
- Poskytnout strojově čitelné i lidsky srozumitelné shrnutí projektu Organomania.
- Zajistit, aby automatizovaní agenti rozuměli doméně, architektuře a akceptovaným pracovním postupům.
- Sloužit jako výchozí referenční bod při diagnostice chyb, návrhu úprav i psaní testů.

## Rychlé seznámení
- **Projekt**: webová aplikace prezentující významné varhany, varhanáře, festivaly a dispozice (Laravel 11, Livewire 3, Bootstrap 5.3).
- **Repo**: logika v `app/`, data v `data/`, šablony v `resources/views`.
- **Cíloví uživatelé**: hudební nadšenci, studenti, správci obsahu.
- **Klíčové scénáře**: prohlížení a filtrování varhan, PDF dispozice, import dat, registrace s podporou AI, sběr statistik.

## Backendová architektura
- **Routování** (`routes/web.php`): Volt Livewire stránky + klasické kontrolery pro exporty a přesměrování.
- **Kontrolery**: `OrganController` vyrábí PDF dispozice (DomPDF) a řeší signed odkazy.
- **Livewire formuláře** (`app/Livewire/Forms`): obsluha editace varhan, dispozic, registrací; `DispositionForm` ukládá klaviatury a rejstříky v transakci.
- **Služby** (`app/Services`):
  - `AI/*` volají OpenAI klienta pro popis dispozic, návrhy registrací, OCR.
  - `VarhanyNetService` scrapuje externí web a mapuje kategorie.
  - Podpůrné služby pro Markdown, geokódování, runtime statistiky atd.
- **Helpers** (`App\Helpers`): formátování dat, práce s řetězci, diakritikou a cache návštěv.
- **Traits/Observery**: `OwnedEntity` + `OwnedEntityScope` (vlastnictví záznamu), `OrganObserver` čistí relace při mazání.

## Doménový model
- `Organ`: lokalita, stavitel, přestavby, dispozice, registrace, statistiky návštěv, likes, vztahy na festivaly a soutěže.
- `Disposition`, `Keyboard`, `DispositionRegister`: struktura dispozic včetně pořadí a parametrů rejstříků.
- `OrganBuilder`, `OrganBuilderTimelineItem`: historie varhanářů a dílen.
- `Registration`, `RegistrationSet`: uložené registrace pro konkrétní skladby.
- `Enums` (např. `Region`, `OrganCategory`, `Pitch`): konzistentní identifikátory.
- `Like`, `Stats`, `VarhanyNetOrgan*`: gamifikace, analytika, historie importu.

## Datové toky
- **Importy** (`php artisan app:import-data {--seed}`): zpracování CSV/Markdown, mapování ID, doplnění kategorií a vztahů na festivaly/soutěže.
- **Scraping** (`VarhanyNetService`): stahuje HTML, extrahuje historii varhan, ukládá serializovaná data pro audit.
- **Statistiky** (`php artisan app:collect-stats {--db} {--mailto=*}`): počítá sumáře, volitelně ukládá do DB a posílá e-maily `StatsCollected`.
- **AI requesty** (`App\Services\AI`): jednotné prompty, normalizované dispozice, logování requestů.
- **PDF exporty**: `OrganController@exportDispositionAsPdf` rendruje Blade šablonu do PDF.

## Uživatelské rozhraní
- **Livewire/Volt** (`resources/views/livewire/pages`): seznamy a detaily varhan, stavitelů, festivalů, soutěží, dispozic, registrací, kvízu.
- **Blade komponenty** (`resources/views/components/organomania`): mapy, tabulky, PDF fragmenty.
- **Layout** (`resources/views/livewire/layout/app-bootstrap.blade.php`): Bootstrap + Livewire skripty.
- **Práva**: `OwnedEntityPolicy` omezuje přístup k soukromým záznamům, `OrganPolicy` má speciální pravidla pro písně.

## AI služby detailně
- `DispositionAI`: normalizuje řádky, případně doplní číslování rejstříků, generuje kontext o nástroji (`getOrganInfo`).
- `DescribeDispositionAI`: vytvoří popis dispozice v aktivním locale, zachovává Markdown a loguje každý dotaz na AI.
- `SuggestRegistrationAI`: vybere rejstříky (čísla v hranatých závorkách), volitelně generuje komentář bez čísel a mapuje je na řádky dispozice pro UI.
- `DispositionOcr`: využívá OpenAI pro převod obrazové dispozice (použití ve `app/Services/AI`).
- Všechny konverzace ukládají auditní stopu do tabulky `ai_request_logs` (obsahuje prompt, odpověď, stav a případnou chybu);
  limity délky odpovědi a retry chování nastavíš v `config/custom.php` (`AI_MAX_RESPONSE_LENGTH`, `AI_RETRY_ATTEMPTS`, `AI_RETRY_SLEEP_MS`).

## CLI nástroje
- `app:import-data` – plný import + volitelně znovu seeduje základ.
- `app:collect-stats` – výpočet statistik, uložení do DB, odeslání e-mailu.
- Další: `app:geocode`, `app:scrape-varhany-net`, `app:update-organists`, `app:collect-stats --schedule` (bez potvrzovacího dotazu).

## Konfigurace a prostředí
- Composer post-skripty vytvářejí `.env`, spouští migrace, aktualizují jazyky.
- `config/custom.php` ovládá bannery, simulace načítání, viditelnost důležitosti varhan/varhanářů.
- `laravel/scout` definuje fulltext nad `Organ`, `OrganBuilder`, `Disposition`.
- Build stack: Vite, Bootstrap, Tailwind (částečně), NPM skripty (`npm run dev`, `npm run build`).
- Monitorování: `opcodesio/log-viewer` pro prohlížení logů v UI.

## Doporučený workflow pro agenty
- **Analýza**: projít relevantní soubory (README, modely, služby) a pochopit dopad.
- **Implementace**: využívat helpery, respektovat vlastnictví entit, při ukládání dispozic držet transakce a generování pořadí.
- **Testování**: `php artisan test`, případně `./vendor/bin/phpstan analyse`; při úpravě assetů spustit `npm run build`.
- **Ověření importu**: po změnách v CSV logice spustit `php artisan app:import-data --seed` nad bezpečnou kopií.
- **Výstup**: v PR shrnout dopad, dopsat kroky pro manuální kontrolu, aktualizovat dokumentaci dle potřeby.

## Checklist před merge
- [ ] Nová dependency je zdokumentovaná v `composer.json` / `package.json`.
- [ ] Existuje test nebo popsaný manuální postup ověření.
- [ ] Livewire formuláře pokrývají edge-case (duplicitní rejstříky, validace ID).
- [ ] AI služby logují chyby a mají fallback, který neblokuje UI.
- [ ] Dokumentace (README, příručka) je aktuální.

## Slovníček pojmů
- **Dispozice**: textový zápis rejstříků, manuálů a pedálů konkrétního varhanního nástroje.
- **Registrace**: kombinace rejstříků pro určený repertoár nebo skladbu.
- **Varhany.net**: externí databáze, ze které se importují historie varhan a varhanářů.
- **Owned entity**: záznam s případným `user_id`, veřejné záznamy ho nemají.
- **Volt stránka**: Livewire komponenta registrovaná přes `Livewire\Volt\Volt::route` a reprezentovaná Blade šablonou v `resources/views/livewire/pages`.
