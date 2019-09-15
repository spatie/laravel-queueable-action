<?php

namespace Spatie\QueueableAction;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ActionMakeCommand extends GeneratorCommand
{
    protected $name = 'make:action';

    protected $description = 'Create a new action class';

    protected $type = 'Action';

    protected function getStub(): string
    {
        return $this->option('sync')
            ? __DIR__.'/stubs/action.stub'
            : __DIR__.'/stubs/action-queued.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Actions';
    }

    protected function getOptions(): array
    {
        return [
            ['sync', null, InputOption::VALUE_NONE, 'Indicates that action should be synchronous'],
        ];
    }
}
