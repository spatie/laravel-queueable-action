<?php

namespace Spatie\QueueableAction;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string */
    protected $actionClass;

    /** @var array */
    protected $parameters;

    /** @var array */
    protected $tags = ['action_job'];

    public function __construct($action, array $parameters = [])
    {
        $this->actionClass = is_string($action) ? $action : get_class($action);
        $this->parameters = $parameters;

        if (is_object($action)) {
            $this->tags = $action->tags();
        }

        $this->resolveQueueableProperties($action);
    }

    public function displayName(): string
    {
        return $this->actionClass;
    }

    public function tags()
    {
        return $this->tags;
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
        ];

        foreach ($queueableProperties as $queueableProperty) {
            $this->{$queueableProperty} = $action->{$queueableProperty} ?? $this->{$queueableProperty};
        }
    }
}
