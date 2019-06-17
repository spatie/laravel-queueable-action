<?php

namespace Spatie\QueueableAction\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ActionMakeCommand extends GeneratorCommand
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:action';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new action class';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Action';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		$stub = 'action.stub';

		return __DIR__ . '/'. $stub;
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string $rootNamespace
	 *
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		return $rootNamespace . '\Actions';
	}
}
