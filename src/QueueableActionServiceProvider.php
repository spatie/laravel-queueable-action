<?php

namespace Spatie\QueueableAction;

use Illuminate\Support\ServiceProvider;

class QueueableActionServiceProvider extends ServiceProvider
{
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
