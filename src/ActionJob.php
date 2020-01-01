<?php

namespace Spatie\QueueableAction;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string */
    protected $actionClass;

    /** @var array */
    protected $parameters;

    public function __construct($action, array $parameters = [])
    {
        $this->actionClass = is_string($action) ? $action : get_class($action);
        $this->parameters = $parameters;

        $this->resolveQueueableProperties($action);
    }

    public function displayName(): string
    {
        return $this->actionClass;
    }

    public function tags()
    {
        return ['action_job'];
    }

    public function handle()
    {
        $action = app($this->actionClass);

        $action->execute(...$this->parameters);
    }

    protected function resolveQueueableProperties($action)
    {
        $queueableProperties = [
            'connection',
            'queue',
            'chainConnection',
            'chainQueue',
            'delay',
            'chained',
            'tries',
            'timeout',
        ];

        foreach ($queueableProperties as $queueableProperty) {
            if (property_exists($action, $queueableProperty)) {
                $this->{$queueableProperty} = $action->{$queueableProperty};
            }
        }
    }
}
