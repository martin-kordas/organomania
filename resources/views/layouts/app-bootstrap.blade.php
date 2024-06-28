@php
use Illuminate\Support\Facades\App;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Organomania') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <link rel="icon" type="image/png" sizes="16x16" href="{{ Vite::asset('resources/images/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ Vite::asset('resources/images/favicon-32x32.png') }}">

        <!-- Scripts -->
        @vite(['resources/css/app-bootstrap.scss', 'resources/js/app.js'])
    </head>
    <body>
        <div class="d-flex flex-column h-100">
            <x-organomania.header />
            
            <!-- Page Content -->
            <main class="container">
                @if (session('status-success'))
                    <div class="alert alert-success">
                        <i class="bi-check-circle-fill"></i> {{ session('status-success') }}
                    </div>
                @endif
                
                {{ $slot }}
            </main>
            
            <a class="back-to-top btn btn-primary position-fixed z-1"><i class="bi-chevron-up"></i></a>
            
            <footer class="container-fluid mt-auto p-0">
                <div class="bg-light border-top mt-4">
                    <div class="container">
                        <div class="d-flex flex-wrap justify-content-between align-items-center py-2">
                            <div class="col-md-4 d-flex align-items-center">
                                <span class="mb-3 mb-md-0 text-body-secondary">© {{ date("Y") }} Martin Kordas</span>
                            </div>

                            <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">O webu</a></li>
                                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Autorská práva</a></li>
                                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Kontakt</a></li>
                                <li class="nav-item"><a href="https://github.com/martin-kordas/organomania" target="_blank" class="nav-link px-2 text-body-secondary"><img class="me-2 align-text-bottom" width="25" height="25" src="{{ Vite::asset('resources/images/github.png') }}" alt="GitHub" /></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
            
        </div>
    </body>
</html>
