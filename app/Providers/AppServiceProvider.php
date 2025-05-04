<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        FilamentAsset::register([
            Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/custom.css'),
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(
            fn ($query) => $this->app->environment('local')
                ? logger()->warning('Lazy loading detected: ' . $query->toSql())
                : null
    );

        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            $class = $model::class;
        
            info("Attempted to lazy load [{$relation}] on model [{$class}].");
        });
 

        if (app()->environment('local', 'staging')) {
            DB::listen(function ($query) {
               File::append(
                   storage_path('/logs/query.log'),
                   $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL
               );
           }); 
       }

    }
}
