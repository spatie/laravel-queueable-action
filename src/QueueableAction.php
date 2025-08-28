<?php

namespace Spatie\QueueableAction;

use Illuminate\Contracts\Queue\Job;
use Spatie\QueueableAction\Exceptions\InvalidConfiguration;

trait QueueableAction
{
    public ?Job $job;

    /**
     * @return static
     */
    public function onQueue(?string $queue = null)
    {
        /** @var self $class */
        $class = new class($this, $queue) {
            protected $action;

            protected $queue;

            public function __construct(object $action, ?string $queue)
            {
                $this->action = $action;
                $this->onQueue($queue);
            }

            public function execute(...$parameters)
            {
                $actionJobClass = $this->determineActionJobClass();

                return dispatch(new $actionJobClass($this->action, $parameters))
                    ->onQueue($this->queue);
            }

            protected function onQueue(?string $queue): void
            {
                if (is_string($queue)) {
                    $this->queue = $queue;

                    return;
                }

                if (isset($this->action->queue)) {
                    $this->queue = $this->action->queue;
                }
            }

            protected function determineActionJobClass(): string
            {
                $actionJobClass = config('queuableaction.job_class') ?? ActionJob::class;

                if (! is_a($actionJobClass, ActionJob::class, true)) {
                    throw InvalidConfiguration::jobClassIsNotValid($actionJobClass);
                }

                return $actionJobClass;
            }
        };

        return $class;
    }

    public function middleware(): array
    {
        return [];
    }

    public function queueMethod(): string
    {
        if (method_exists($this, '__invoke')) {
            return '__invoke';
        }

        return 'execute';
    }

    /**
     * @return array|int
     */
    public function backoff()
    {
        return $this->backoff ?? [];
    }

    /**
     * @return string[]
     */
    public function tags(): array
    {
        return [self::class];
    }
}
