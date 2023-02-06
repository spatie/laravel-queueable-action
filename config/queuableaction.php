<?php

return [
    /*
     * The job class that will be dispatched.
     * If you would like to change it and use your own job class,
     * it must extend the \Spatie\QueueableAction\ActionJob class.
     */
    'job_class' => \Spatie\QueueableAction\ActionJob::class,

    /*
     * The job class that will be dispatched for unique jobs.
     * If you would like to change it and use your own job class,
     * it must extend the \Spatie\QueueableAction\ActionJob class.
     */
    'unique_job_class' => \Spatie\QueueableAction\UniqueActionJob::class,
];
