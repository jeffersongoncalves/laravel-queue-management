<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Queue Tables
    |--------------------------------------------------------------------------
    |
    | The database table names used by Laravel's "database" queue driver.
    | Override these if your application uses custom table names for the
    | jobs, failed jobs and job batches tables.
    |
    */
    'tables' => [
        'jobs' => 'jobs',
        'failed_jobs' => 'failed_jobs',
        'job_batches' => 'job_batches',
    ],
];
