<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Livewire\Volt\Volt;
use App\Http\Controllers\AboutOrganController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WelcomeController;

//dd(preg_replace('/[0-9]+\. /u', '', '1. Pomezí2. , Ušovice u Mariánských Lázní '));
// více varhanářů: 210
Route::get('scrape', function () {
    set_time_limit(0);
    $service = app(App\Interfaces\GeocodingService::class);
    $organs = App\Models\Organ::where('user_id', 5)->where('latitude', 0)->where('id', '>=', 4101)/*->where('id', 4280)*/->orderBy('id')->take(1000)->get();
    foreach ($organs as $organ) {
        $address = "{$organ['municipality']} {$organ['place']}";
        //dd($address);
        try {
            $res = $service->geocode($address);
            $organ->latitude = $res['latitude'];
            $organ->longitude = $res['longitude'];
            $organ->region_id = $res['regionId'];
            $organ->save();
            dump("Úspěšně zjištěna pozice varhan (organId: {$organ->id})");
        }
        catch (RuntimeException $ex) {
            if ($ex->getMessage() === 'Pozice nebyla nalezena.') dump("CHYBA! Nenalezena přesná pozice (organId: {$organ->id})");
            else throw $ex;
        }
        //dd($res);
    }
    return;
    
    $res = $service->geocode('kostel sv. Tomáše Praha, Malá Strana');
    dd($res);
    $res['geometry']['location']['lat'];
    $res['geometry']['location']['long'];
    
    
    /*$organBuilders = \App\Models\OrganBuilder::where('user_id', 5)->orderBy('id')->get();
    foreach ($organBuilders as $organBuilder) {
        if ($organBuilder->active_from_year !== 9999) {
            $item = new \App\Models\OrganBuilderTimelineItem;
            $item->loadFromOrganBuilder($organBuilder);
            $item->save();
       }
    }*/
    return;
    
    
    Illuminate\Support\Facades\Artisan::call('app:scrape-varhany-net', ['startOrganId' => 210]);
    
    return;
    $serv = new App\Services\VarhanyNetService;
    $scraped = $serv->scrapeOrgan(210);
    dd($scraped);
    $scraped['organ']->save();
    $categoryIds = $scraped['organCategories']->pluck('value');
    
    
    $scraped['organ']->organRebuilds()->sync($categoryIds);
    return;
    
    $serv = new App\Services\VarhanyNetService;
    //$serv->scrapeOrgan(539);
    $scraped = $serv->scrapeOrganBuilder(158795);
    $scraped['organBuilder']->save();
    $categoryIds = $scraped['organBuilderCategories']->pluck('value');
    $scraped['organBuilder']->organBuilderCategories()->sync($categoryIds);
});

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
    
    Route::middleware('can:useRegistrationSets')->group(function () {
        Volt::route('dispositions/{disposition}/registration-sets', 'pages.registration-sets')
            ->name('dispositions.registration-sets.index');
        Volt::route('dispositions/{disposition}/registration-sets/create', 'pages.registration-set-edit')
            ->name('dispositions.registration-sets.create');
        Volt::route('dispositions/{disposition}/registration-sets/{registrationSet}/edit', 'pages.registration-set-edit')
            ->name('dispositions.registration-sets.edit')
            ->whereNumber('registrationSet')
            ->scopeBindings();
    });
    
    Volt::route('test', 'pages.test');
});

Route::get('/', WelcomeController::class)
    ->name('welcome');
Route::view('about', 'about')
    ->name('about');
Route::view('donate', 'donate')
    ->name('donate');
Route::view('links', 'links')
    ->name('links');
Route::get('about-organ', AboutOrganController::class)
    ->name('about-organ');

Route::get('sitemap.xml', SitemapController::class);
Route::get('qr', QrController::class);

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
Volt::route('organs/{organSlug}', 'pages.organ-show')
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
Volt::route('festivals/{festival}', 'pages.festival-show')
    ->name('festivals.show');

Volt::route('competitions', 'pages.competitions')
    ->name('competitions.index');
Volt::route('competitions/{competition}', 'pages.competition-show')
    ->name('competitions.show');

Volt::route('dispositions', 'pages.dispositions')
    ->name('dispositions.index');
Volt::route('dispositions/diff', 'pages.disposition-diff')
    ->name('dispositions.diff');
Volt::route('dispositions/registers', 'pages.registers')
    ->name('dispositions.registers.index');
Volt::route('dispositions/registers/{registerName}', 'pages.register-show')
    ->name('dispositions.registers.show');
Volt::route('dispositions/{dispositionSlug}', 'pages.disposition-show')
    ->name('dispositions.show');
Volt::route('dispositions/{disposition}/registration-sets/{registrationSet}', 'pages.registration-set-show')
    ->name('dispositions.registration-sets.show')
    ->scopeBindings();

Volt::route('organists', 'pages.organists')
    ->name('organists.index');

Route::get('organ-custom-categories/{id}/organs', function ($id) {
    $params = ['filterCategories' => ["custom-$id"], 'viewType' => 'table'];
    if (request()->hasValidSignature(false)) {
        $relativeUrl = URL::signedRoute('organs.custom-category-organs.index', $params, absolute: false);
        return redirect($relativeUrl);
    }
    else return redirect()->route('organs.index', $params);
})->name('organs.organ-custom-categories.organs');

Route::get('organ-builders-custom-categories/{id}/organ-builders', function ($id) {
    $params = ['filterCategories' => ["custom-$id"], 'viewType' => 'table'];
    if (request()->hasValidSignature(false)) {
        $relativeUrl = URL::signedRoute('organ-builders.custom-category-organ-builders.index', $params, absolute: false);
        return redirect($relativeUrl);
    }
    else return redirect()->route('organ-builders.index', $params);
})->name('organ-builders.organ-builder-custom-categories.organ-builders');

require __DIR__.'/auth.php';
