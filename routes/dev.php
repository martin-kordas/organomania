<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Disposition;

Route::get('testPdf/{name}', function ($name) {
    $parameters = match ($name) {
        'disposition' => [
            'disposition' => Disposition::query()
                ->with(['keyboards' => function (HasMany $query) {
                    $query->withCount('realDispositionRegisters');
                }])
                ->findOrFail(1),
            ],
        default => [],
    };
    
    return view("components.organomania.pdf.$name", $parameters);
});