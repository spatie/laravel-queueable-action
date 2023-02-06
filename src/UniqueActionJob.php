<?php

namespace Spatie\QueueableAction;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
}
