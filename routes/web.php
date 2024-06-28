<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(["auth"])->group(function () {
    Volt::route('organs', 'pages.organs')
        ->name('organs.index');
    Volt::route('organs/{organ}', 'pages.organ-show')
        ->name('organs.show')
        ->whereNumber('organ');
    
    Volt::route('organ-builders', 'pages.organ-builders')
        ->name('organ-builders.index');
    Volt::route('organ-builders/{organBuilder}', 'pages.organ-builder-show')
        ->name('organ-builders.show')
        ->whereNumber('organBuilder');
    Volt::route('organ-builders/create', 'pages.organ-builder-edit')
        ->name('organ-builders.create');
    Volt::route('organ-builders/{organBuilder}/edit', 'pages.organ-builder-edit')
        ->name('organ-builders.edit')
        ->where('organBuilder', '[0-9]+|new');
});

require __DIR__.'/auth.php';
