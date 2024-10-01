@php
use Illuminate\Support\Facades\App;

$googleMapsScript = url()->query('https://maps.googleapis.com/maps/api/js', [
    'key' => env('GOOGLE_API_KEY'),
    'libraries' => 'maps,marker',
    'v' => 'beta'
]);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
            @isset($title) {{ $title }} | @endisset
            {{ config('app.name', 'Organomania') }}
        </title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <link rel="icon" type="image/png" sizes="16x16" href="{{ Vite::asset('resources/images/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ Vite::asset('resources/images/favicon-32x32.png') }}">

        @if (!config('app.debug'))
            <script async src="https://www.googletagmanager.com/gtag/js?id=G-3NVMH2JEBV"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', 'G-3NVMH2JEBV');
            </script>
        @endif
        
        @vite([
            'resources/css/app-bootstrap.scss',
            'resources/js/app.js',
            // nešlo importovat v app.js, protože skript vyžaduje globální jQuery, které při importu jako modul není dostupné
            //  - kvůli nefunkčnosti na produkci zakomentováno úplně
            //'node_modules/select2/dist/js/i18n/cs.js'
        ])
        @stack('styles')
        @stack('scripts')
        
        @if (request()->routeIs(['organs.index', 'organs.show', 'organ-builders.index', 'organ-builders.show', 'festivals.index']))
            <script src="{{ $googleMapsScript }}" defer></script>
        @endif    
    </head>
    <body>
        <div class="d-flex flex-column" style="min-height: 100%;">
            <x-organomania.header />
            
            <main class="container">
                @if (session('status-success'))
                    <div class="alert alert-success">
                        <i class="bi-check-circle-fill"></i> {{ session('status-success') }}
                    </div>
                @endif
                
                {{ $slot }}
            </main>
            
            <footer class="container-fluid mt-auto p-0 d-print-none">
                <div class="bg-light border-top mt-4">
                    <div class="container">
                        <div class="d-flex flex-wrap justify-content-between align-items-center py-2">
                            <div class="">
                                <span class="mb-3 mb-md-0 text-body-secondary">
                                    © {{ date("Y") }}
                                    <a href="martin-kordas" class="link-secondary text-decoration-none">Martin Kordas</a>
                                </span>
                            </div>

                            <ul class="nav ms-lg-auto list-unstyled">
                                @if (Gate::allows('viewLogViewer'))
                                    <x-organomania.footer-nav-item href="/log-viewer">
                                        {{ __('Log') }}
                                    </x-organomania.footer-nav-item>
                                @endif
                                    
                                {{--<x-organomania.footer-nav-item href="#">
                                    {{ __('O webu') }}
                                </x-organomania.footer-nav-item>--}}
                                {{--<x-organomania.footer-nav-item href="#">
                                    {{ __('Autorská práva') }}
                                </x-organomania.footer-nav-item>--}}
                                <x-organomania.footer-nav-item href="mailto:{{ config('custom.app_admin_email') }}">
                                    {{ __('Kontakt') }}
                                </x-organomania.footer-nav-item>
                                <x-organomania.footer-nav-item href="https://github.com/martin-kordas/organomania" target="_blank">
                                    <img class="me-2 align-text-bottom" width="25" height="25" src="{{ Vite::asset('resources/images/github.png') }}" alt="GitHub" />
                                </x-organomania.footer-nav-item>
                                    
                                <li class="nav-item"><a href="https://github.com/martin-kordas/organomania" target="_blank" class="nav-link px-2 text-body-secondary"></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
                
            <a class="back-to-top btn btn-primary position-fixed z-1 d-print-none" onclick="scrollToTop()">
                <i class="bi-chevron-up"></i>
            </a>
        </div>
    </body>
</html>
