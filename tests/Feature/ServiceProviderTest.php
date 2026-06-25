<?php

it('publishes the configuration file', function () {
    expect(config('queue-management.tables.jobs'))->toBe('jobs')
        ->and(config('queue-management.tables.failed_jobs'))->toBe('failed_jobs')
        ->and(config('queue-management.tables.job_batches'))->toBe('job_batches');
});
