<header class="p-2 mb-4 border-bottom position-sticky top-0 z-3 bg-light">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="{{ route('organs.index') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
                <img class="logo me-2" src="{{ Vite::asset('resources/images/logo.png') }}" />
                <span class="fs-4">{{ config('app.name', 'Organomania') }}</span>
            </a>

            <ul class="nav nav-pills col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li class="nav-item"><a href="{{ route('organs.index') }}" wire:navigate @class(['nav-link', 'px-3', 'active' => request()->routeIs('organs.*')])'><i class="bi-file-music"></i> {{ __('Varhany') }}</a></li>
                <li class="nav-item"><a href="{{ route('organ-builders.index') }}" wire:navigate @class(['nav-link', 'px-3', 'active' => request()->routeIs('organ-builders.*')])'><i class="bi-file-person"></i> {{ __('Varhanáři') }}</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-3 disabled" aria-disabled="true"><i class="bi-calendar-event"></i> {{ __('Festivaly') }}</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-3 disabled" aria-disabled="true"><i class="bi-vinyl"></i> {{ __('Rejstříky') }}</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-3 disabled" aria-disabled="true"><i class="bi-card-list"></i> {{ __('Dispozice') }}</a></li>
            </ul>

            <livewire:search />

            <div class="dropdown text-end">
                <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ Vite::asset('resources/images/user.png') }}" alt="mdo" width="32" height="32" class="rounded-circle">
                </a>
                <ul class="dropdown-menu text-small">
                    <li><a class="dropdown-item disabled" href="#">{{ __('Nastavení') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('profile') }}">{{ __('Profil') }}</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('logout') }}">{{ __('Odhlásit se') }}</a></li>
                </ul>
            </div>

            <div class="dropdown text-end ms-3" data-bs-toggle="tooltip" data-bs-title="{{ __('Změnit jazyk') }}">
                <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi-globe fs-5 align-middle"></i>
                </a>
                <ul class="dropdown-menu text-small">
                    <li><a @class(['dropdown-item', 'active' => App::isLocale('cs')]) href="{{ request()->fullUrlWithQuery(['lang' => 'cs']) }}">&#127464;&#127487; Česky (cs)</a></li>
                    <li><a @class(['dropdown-item', 'active' => App::isLocale('en')]) href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}">&#127468;&#127463; English (en)</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>
