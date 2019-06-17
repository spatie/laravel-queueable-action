<?php

namespace Spatie\QueueableAction;

use Illuminate\Support\ServiceProvider;
use Spatie\QueueableAction\Commands\ActionMakeCommand;

class QueueableActionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind('command.make:action', ActionMakeCommand::class);

        $this->commands([
        	'command.make:action'
        ]);
    }
}
