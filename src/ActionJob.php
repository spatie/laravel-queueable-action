<?php

namespace Spatie\QueueableAction;

use DateTime;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, Batchable;

    use SerializesModels {
        __serialize as serializesModelsSerialize;
        __unserialize as serializesModelsUnserialize;
    }

    /** @var string */
    protected $actionClass;

    /** @var array */
    protected $parameters;

    /** @var array */
    protected $tags = ['action_job'];

    /** @var callable */
    protected $onFailCallback;

    protected $backoff;

    /** @var DateTime|null */
    protected $retryUntil;

    public function __construct($action, array $parameters = [])
    {
        $this->actionClass = is_string($action) ? $action : get_class($action);
        $this->parameters = $parameters;

        if (is_object($action)) {
            $this->tags = $action->tags();
            $this->middleware = $action->middleware();

            if (method_exists($action, 'backoff')) {
                $this->backoff = $action->backoff();
            }

            if (method_exists($action, 'retryUntil')) {
                $this->retryUntil = $action->retryUntil();
            }

            if (method_exists($action, 'failed')) {
                $this->onFailCallback = [$action, 'failed'];
            }
        }

        $this->resolveQueueableProperties($this->actionClass);
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
        return [];
    }

    public function parameters()
    {
        return $this->parameters;
    }

    public function backoff()
    {
        return $this->backoff;
    }

    public function retryUntil()
    {
        return $this->retryUntil;
    }

    public function failed(Throwable $exception)
    {
        if ($this->onFailCallback) {
            return ($this->onFailCallback)($exception);
        }
    }

    public function handle()
    {
        $action = app($this->actionClass);
        $action->job = $this->job;
        $action->{$action->queueMethod()}(...$this->parameters);
    }

    public function __serialize()
    {
        foreach ($this->parameters as $index => $parameter) {
            $this->parameters[$index] = $this->getSerializedPropertyValue($parameter);
        }

        return $this->serializesModelsSerialize();
    }

    public function __unserialize(array $values)
    {
        $this->serializesModelsUnserialize($values);

        foreach ($this->parameters as $index => $parameter) {
            $this->parameters[$index] = $this->getRestoredPropertyValue($parameter);
        }

        return $values;
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
            'maxExceptions',
            'retryUntil',
        ];

        foreach ($queueableProperties as $queueableProperty) {
            if (property_exists($action, $queueableProperty)) {
                $this->{$queueableProperty} = app($action)->{$queueableProperty};
            }
        }
    }
}
