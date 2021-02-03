<?php

namespace Spatie\QueueableAction;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class QueueableActionServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/queuableaction.php' => config_path('queuableaction.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/queuableaction.php', 'queuableaction');
    }

    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ActionMakeCommand::class,
            ]);
        }
    }

    public function provides(): array
    {
        return [
            ActionMakeCommand::class,
        ];
    }
}
