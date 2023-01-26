<?php

namespace SeiyuNico\LaravelCore\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use SeiyuNico\LaravelCore\Console\Commands\RepositoryMakeCommand;
use SeiyuNico\LaravelCore\Console\Commands\ServiceMakeCommand;

class LaravelCoreProviders extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('getOr', function ($callback) {
            /** @var \Illuminate\Database\Eloquent\Model $this */
            return tap($this->get(), fn ($results) => ($results->isNotEmpty()) ? $results : call_user_func($callback));
        });

        $this->commands([
            ServiceMakeCommand::class,
            RepositoryMakeCommand::class,
        ]);
    }
}
