<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

class ContinueMiddleware
{
    public function handle($job, $next)
    {
        $next($job);
    }
}
