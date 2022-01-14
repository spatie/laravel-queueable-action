<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

class CountRunsMiddleware
{
    public function handle($job, $next)
    {
        $_SERVER['_test_run_count_middleware']++;

        $next($job);
    }
}
