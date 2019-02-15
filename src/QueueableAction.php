<?php

namespace Spatie\QueueableAction;

trait QueueableAction
{
    /**
     * @return static
     */
    public function onQueue(bool $useQueue = true)
    {
        if (! $useQueue) {
            return $this;
        }

        /** @var self $class */
        $class = new class($this)
        {
            protected $action;

            public function __construct(object $action)
            {
                $this->action = $action;
            }

            public function execute(...$parameters)
            {
                return dispatch(new ActionJob($this->action, $parameters));
            }
        };

        return $class;
    }
}
