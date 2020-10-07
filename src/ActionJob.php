<?php

namespace Spatie\QueueableAction;

use Exception;
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

    /** @var array */
    protected $tags = ['action_job'];

    /** @var callable */
    protected $onFailCallback;

    public function __construct($action, array $parameters = [])
    {
        $this->actionClass = is_string($action) ? $action : get_class($action);
        $this->parameters = $parameters;

        if (is_object($action)) {
            $this->tags = $action->tags();
            $this->middleware = $action->middleware();

            $this->middleware = $action->middleware();

            if (method_exists($action, 'failed')) {
                $this->onFailCallback = [$action, 'failed'];
            }
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

    public function middleware()
    {
        return $this->middleware;
    }

    public function failed(Exception $exception)
    {
        if ($this->onFailCallback) {
            return ($this->onFailCallback)($exception);
        }
    }

    public function handle()
    {
        $action = app($this->actionClass);
        if (property_exists($action, 'queueMethod')) {
            $action->{$action->queueMethod}(...$this->parameters);
        } else {
            $action->execute(...$this->parameters);
        }
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
