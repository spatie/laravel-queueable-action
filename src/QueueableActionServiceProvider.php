<?php

namespace Spatie\QueueableAction;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class QueueableActionServiceProvider extends ServiceProvider implements DeferrableProvider
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
