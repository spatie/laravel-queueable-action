<?php

return [
    /*
     * The job class that will be dispatched.
     * If you would like to change it and use your own job class,
     * it must extends the \Spatie\QueueableAction\ActionJob class.
     */
    'job_class' => \Spatie\QueueableAction\ActionJob::class,
    
    /*
     * Public method settings for an action.
     * The first execution option is given by the __invoke hook.
     * if you want to configure a different method update the value e.g. run.
     */
    'executable_action_method' => 'execute',
];
