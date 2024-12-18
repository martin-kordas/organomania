@use(App\Models\Festival)
@use(App\Models\Competition)

<header class="mb-4 border-bottom position-sticky top-0 d-print-none">
    <nav class="navbar navbar-expand-xl">
        <div class="container d-flex flex-wrap align-items-center">
            {{-- logo --}}
            <a href="{{ url('/') }}" wire:navigate class="d-flex align-items-center mb-md-0 me-4 link-body-emphasis text-decoration-none">
                <img class="logo me-2" src="{{ Vite::asset('resources/images/logo.png') }}" />
                <span class="app-name fs-4 lh-1" style="font-size: 140% !important;">
                    <span>{{ str(config('app.name', 'Organomania'))->lower() }}</span>
                    <br />
                    <span class="app-subtitle fst-italic" style="font-size: 65%;">{{ __('varhany v České republice') }}</span>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="{{ __('Zobrazit navigaci') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse gap-3 mt-2 mt-xl-0" id="navbarCollapse">
                {{-- vlastní navigace --}}
                <ul class="nav nav-pills justify-content-center row-gap-1">
                    <x-organomania.nav-item-route label="{{ __('Varhany') }}" icon="music-note-list" route="organs.index" routeActive="organs.*" />
                    <x-organomania.nav-item-route label="{{ __('menu.organ-builders') }}" icon="person-circle" route="organ-builders.index" routeActive="organ-builders.*" />
                    <x-organomania.nav-item-route label="{{ __('Dispozice') }}" icon="card-list" route="dispositions.index" routeActive="dispositions.*" />
                    <div class="w-100 d-sm-none"></div>
                    <x-organomania.nav-item-route label="{{ __('Festivaly') }}" icon="calendar-date" route="festivals.index" routeActive="festivals.*" :highlightedCount="Festival::getHighlightedCount()" />
                    <x-organomania.nav-item-route label="{{ __('Soutěže') }}" icon="trophy" route="competitions.index" routeActive="competitions.*" :highlightedCount="Competition::getHighlightedCount()" />
                </ul>

                <div class="row gx-2 gy-2 gy-xl-0 my-1 ms-auto align-items-center">
                    {{-- hledání --}}
                    <livewire:search />
                    
                    {{-- nastavení jazyka --}}
                    <div class="dropdown text-end col-auto" data-bs-toggle="tooltip" data-bs-title="{{ __('Změnit jazyk') }}">
                        <a href="#" class="d-block btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi-translate  align-middle"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end text-small">
                            <li><a @class(['dropdown-item', 'active' => App::isLocale('cs')]) href="{{ request()->fullUrlWithQuery(['lang' => 'cs']) }}">&#127464;&#127487; Česky (cs)</a></li>
                            <li><a @class(['dropdown-item', 'active' => App::isLocale('en')]) href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}">&#127468;&#127463; English (en)</a></li>
                        </ul>
                    </div>

                    {{-- možnosti přihlášení --}}
                    @if (Auth::check())
                        <div class="dropdown text-end col-auto">
                            <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ Vite::asset('resources/images/user.png') }}" alt="mdo" width="32" height="32" class="rounded-circle">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <div class="dropdown-header text-center">
                                        <strong>{{ Auth::user()->name }}</strong>
                                        @if (Auth::user()->admin)
                                            <br />
                                            <small class="text-secondary">({{ __('administrátor') }})</small>
                                        @endif
                                        <br />
                                        {{ Auth::user()->email }}
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('profile') }}">{{ __('Profil') }}</a></li>
                                <li><a class="dropdown-item disabled" href="#">{{ __('Nastavení') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('logout') }}">{{ __('Odhlásit se') }}</a></li>
                            </ul>
                        </div>
                    @else
                        <div class="col-auto">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('login') }}">
                                <i class="bi-box-arrow-in-right"></i>
                                <span class="d-none d-xxl-inline-block">{{ __('Přihlášení') }}</span>
                            </a>
                            <a class="btn btn-sm btn-hover text-secondary d-none d-xl-inline-block" href="{{ route('register') }}">
                                {{ __('Registrace') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>
</header>
