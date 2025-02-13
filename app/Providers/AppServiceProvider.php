<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use NumberFormatter;
use OpenAI;
use OpenAI\Contracts\ClientContract;
use App\Repositories\OrganRepository;
use App\Interfaces\GeocodingService;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\Disposition;
use App\Models\Competition;
use App\Models\Festival;
use App\Models\RegistrationSet;
use App\Models\RegisterName;
use App\Models\User;
use App\Models\Scopes\OwnedEntityScope;
use App\Listeners\EntityEventSubscriber;
use App\Services\MapyCzGeocodingService;
use App\Console\Commands\ImportData;
use Database\Seeders\DatabaseSeeder;

class AppServiceProvider extends ServiceProvider
{
    
    public $bindings = [
        
    ];
    
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->when(ImportData::class)
            ->needs(DatabaseSeeder::class)
            ->give(function () {
                //$seeder = $this->app->make(DatabaseSeeder::class);
                $seeder = new DatabaseSeeder;
                $seeder->defaultOnly = true;
                return $seeder;
            });
            
        $this->app->bind(ClientContract::class, function () {
            $apiKey = getenv('OPENAI_API_KEY');
            return OpenAI::client($apiKey);
        });
        
        $this->app->bind(GeocodingService::class, MapyCzGeocodingService::class);
        
        $this->app
            ->when(NumberFormatter::class)
            ->needs('$locale')
            ->give(
                fn() => app()->getLocale()
            );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        
        Gate::define('viewLogViewer', function (?User $user) {
            return config('app.env') === 'local' || $user?->isAdmin();
        });
        Gate::define('deleteLogFile', function (?User $user) {
            return $user?->name === 'Admin';
        });
        Gate::define('deleteLogFolder', function (?User $user) {
            return $user?->name === 'Admin';
        });
        
        Gate::define('likeOrgans', function (User $user) {
            return true;        // pro všechny přihlášené
        });
        Gate::define('likeOrgan', function (User $user, Organ $organ) {
            return !isset($organ->user_id);
        });
        Gate::define('likeOrganBuilders', function (User $user) {
            return true;        // pro všechny přihlášené
        });
        Gate::define('likeOrganBuilder', function (User $user, OrganBuilder $organBuilder) {
            return !isset($organBuilder->user_id);
        });
        
        Gate::define('useOrganCustomCategories', function (User $user) {
            return true;        // pro všechny přihlášené
        });
        Gate::define('useOrganBuilderCustomCategories', function (User $user) {
            return true;        // pro všechny přihlášené
        });
        Gate::define('useRegistrationSets', function (User $user) {
            return true;        // pro všechny přihlášené
        });
        
        Gate::define('likeOrganists', function (User $user) {
            return true;        // pro všechny přihlášené
        });
        
        Gate::define('useAI', function (?User $user) {
            return $user?->isAdmin() || $user?->premium;
        });
        
        $modelBindings = [
            'organ' => Organ::class,
            'organBuilder' => OrganBuilder::class,
            'disposition' => Disposition::class,
            'festival' => Festival::class,
            'competition' => Competition::class,
            'registerName' => RegisterName::class,
            'registrationSet' => RegistrationSet::class,
        ];
        foreach ($modelBindings as $modelBinding => $modelClass) {
            Route::bind($modelBinding, function (string $value) use ($modelClass) {
                $query = $modelClass::query();
                // pro správný route model binding potlačíme OwnedEntityScope
                if (request()->hasValidSignature(false)) {
                    $query->withoutGlobalScope(OwnedEntityScope::class);
                }
                // podpora slugu
                //     - TODO: je-li slug číselný, bude ho chybně vyhledávat jako id
                if (is_numeric($value)) $query->where('id', $value);
                else $query->where('slug', $value);
                return $query->firstOrFail();
            });
        }
    }
}
