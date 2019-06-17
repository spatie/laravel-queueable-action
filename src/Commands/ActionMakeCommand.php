<?php

namespace Spatie\QueueableAction\Commands;

use Illuminate\Console\GeneratorCommand;

class ActionMakeCommand extends GeneratorCommand
{
	protected $name = 'make:action';

	protected $description = 'Create a new action class';

	protected $type = 'Action';

	protected function getStub()
	{
        return __DIR__.'/../../stubs/action.stub';
    }

	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace.'\Actions';
	}
}
