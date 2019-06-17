<?php

namespace Spatie\QueueableAction;

use Illuminate\Support\ServiceProvider;
use Spatie\QueueableAction\Commands\ActionMakeCommand;

class QueueableActionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind('command.queueable-action:make', ActionMakeCommand::class);

        $this->commands([
        	'command.queueable-action:make'
        ]);
    }
}
