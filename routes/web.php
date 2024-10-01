<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\ExportController;

Route::middleware(["auth"])->group(function () {
    Volt::route('dispositions/create', 'pages.disposition-edit')
        ->name('dispositions.create');
    Volt::route('dispositions/{disposition}/edit', 'pages.disposition-edit')
        ->name('dispositions.edit')
        ->whereNumber('disposition');
    
    Volt::route('organs/create', 'pages.organ-edit')
        ->name('organs.create');
    Volt::route('organs/{organ}/edit', 'pages.organ-edit')
        ->name('organs.edit')
        ->whereNumber('organ');
    
    Volt::route('organ-builders/create', 'pages.organ-builder-edit')
        ->name('organ-builders.create');
    Volt::route('organ-builders/{organBuilder}/edit', 'pages.organ-builder-edit')
        ->name('organ-builders.edit')
        ->whereNumber('organBuilder');
    
    Route::middleware('can:useOrganCustomCategories')->group(function () {
        Volt::route('organ-custom-categories', 'pages.organ-custom-categories')
            ->name('organs.organ-custom-categories');
    });
    Route::middleware('can:useOrganBuilderCustomCategories')->group(function () {
        Volt::route('organ-builder-custom-categories', 'pages.organ-custom-categories')
            ->name('organ-builders.organ-builder-custom-categories');
    });
    
    Volt::route('test', 'pages.test');
});

Route::view('/', 'welcome')
    ->name('welcome');

// zatím nelze smazat, protože na routu se odkazuje
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Volt::route('organs', 'pages.organs')
    ->name('organs.index');
Volt::route('custom-category-organs', 'pages.organs')
    ->name('organs.custom-category-organs.index');
Volt::route('organs/{organ}', 'pages.organ-show')
    ->name('organs.show');
Route::get('export/organs', [ExportController::class, 'exportOrgans'])
    ->name('organs.export');

Volt::route('organ-builders', 'pages.organ-builders')
    ->name('organ-builders.index');
Volt::route('custom-category-organ-builders', 'pages.organ-builders')
    ->name('organ-builders.custom-category-organ-builders.index');
Volt::route('organ-builders/{organBuilder}', 'pages.organ-builder-show')
    ->name('organ-builders.show');
Route::get('export/organ-builders', [ExportController::class, 'exportOrganBuilders'])
    ->name('organ-builders.export');

Volt::route('festivals', 'pages.festivals')
    ->name('festivals.index');

Volt::route('dispositions', 'pages.dispositions')
    ->name('dispositions.index');
Volt::route('dispositions/diff', 'pages.disposition-diff')
    ->name('dispositions.diff');
Volt::route('dispositions/registers', 'pages.registers')
    ->name('dispositions.registers.index');
Volt::route('dispositions/{disposition}', 'pages.disposition-show')
    ->name('dispositions.show');

Route::get('organ-custom-categories/{id}/organs', function ($id) {
    $params = ['filterCategories' => ["custom-$id"], 'viewType' => 'table'];
    if (request()->hasValidSignature()) {
        return redirect()->signedRoute('organs.custom-category-organs.index', $params);
    }
    else return redirect()->route('organs.index', $params);
})->name('organs.organ-custom-categories.organs');

Route::get('organ-builders-custom-categories/{id}/organ-builders', function ($id) {
    $params = ['filterCategories' => ["custom-$id"], 'viewType' => 'table'];
    if (request()->hasValidSignature()) {
        return redirect()->signedRoute('organ-builders.custom-category-organ-builders.index', $params);
    }
    else return redirect()->route('organ-builders.index', $params);
})->name('organ-builders.organ-builder-custom-categories.organ-builders');

require __DIR__.'/auth.php';
