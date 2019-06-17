<?php

namespace Spatie\QueueableAction;

use Illuminate\Support\ServiceProvider;
use Spatie\QueueableAction\Console\ActionMakeCommand;

class QueueableActionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('command.make:action', ActionMakeCommand::class);

        $this->commands([
        	'command.make:action'
        ]);
    }
}
