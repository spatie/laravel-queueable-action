<?php

namespace Spatie\QueueableAction;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Throwable;

class UniqueActionJob extends ActionJob implements ShouldBeUnique
{
    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return (string) ($this->uniqueId ?? '');
    }

    /**
     * Get the cache driver for the unique job lock.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function uniqueVia()
    {
        return Cache::driver($this->uniqueVia ?? config('cache.default'));
    }
}
