<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use JeffersonGoncalves\QueueManagement\Models\FailedJob;

function createFailedJob(array $overrides = []): FailedJob
{
    return FailedJob::query()->create(array_merge([
        'uuid' => (string) Str::uuid(),
        'connection' => 'database',
        'queue' => 'default',
        'payload' => ['displayName' => 'App\\Jobs\\SendEmail', 'maxTries' => 2, 'delay' => 10],
        'exception' => 'Something went wrong',
    ], $overrides));
}

it('reads the table name from config', function () {
    config()->set('queue-management.tables.failed_jobs', 'custom_failed_jobs');

    expect((new FailedJob)->getTable())->toBe('custom_failed_jobs');
});

it('casts the payload to an array and failed_at to a datetime', function () {
    $failedJob = createFailedJob()->refresh();

    expect($failedJob->payload)->toBeArray()
        ->and($failedJob->failed_at)->toBeInstanceOf(Carbon::class);
});

it('appends the payload accessors', function () {
    $failedJob = createFailedJob();

    expect($failedJob->displayName)->toBe('App\\Jobs\\SendEmail')
        ->and($failedJob->maxTries)->toBe(2)
        ->and($failedJob->delay)->toBe(10);
});

it('auto-sets failed_at on save', function () {
    Carbon::setTestNow($now = Carbon::createFromTimestamp(1_700_000_000));

    $failedJob = createFailedJob();

    expect($failedJob->failed_at->timestamp)->toBe($now->timestamp);

    Carbon::setTestNow();
});
