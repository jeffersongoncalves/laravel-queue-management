<?php

use Illuminate\Support\Carbon;
use JeffersonGoncalves\QueueManagement\Models\Job;

it('reads the table name from config', function () {
    config()->set('queue-management.tables.jobs', 'custom_jobs');

    expect((new Job)->getTable())->toBe('custom_jobs');
});

it('casts the payload to an array', function () {
    $job = Job::query()->create([
        'queue' => 'default',
        'payload' => ['displayName' => 'App\\Jobs\\SendEmail', 'maxTries' => 3, 'delay' => 60],
        'attempts' => 0,
    ]);

    expect($job->refresh()->payload)->toBeArray()
        ->and($job->payload['displayName'])->toBe('App\\Jobs\\SendEmail');
});

it('appends displayName, maxTries and delay accessors from the payload', function () {
    $job = Job::query()->create([
        'queue' => 'default',
        'payload' => ['displayName' => 'App\\Jobs\\SendEmail', 'maxTries' => 5, 'delay' => 30],
        'attempts' => 0,
    ]);

    expect($job->displayName)->toBe('App\\Jobs\\SendEmail')
        ->and($job->maxTries)->toBe(5)
        ->and($job->delay)->toBe(30)
        ->and($job->toArray())->toHaveKeys(['displayName', 'maxTries', 'delay']);
});

it('returns sensible defaults when payload keys are missing', function () {
    $job = Job::query()->create([
        'queue' => 'default',
        'payload' => [],
        'attempts' => 0,
    ]);

    expect($job->displayName)->toBe('')
        ->and($job->maxTries)->toBe(0)
        ->and($job->delay)->toBe(0);
});

it('auto-sets available_at and created_at on save', function () {
    Carbon::setTestNow(Carbon::createFromTimestamp(1_700_000_000));

    $job = Job::query()->create([
        'queue' => 'default',
        'payload' => [],
        'attempts' => 0,
    ]);

    expect($job->available_at)->toBe(1_700_000_000)
        ->and($job->created_at)->toBe(1_700_000_000);

    Carbon::setTestNow();
});

it('does not have timestamps', function () {
    expect((new Job)->usesTimestamps())->toBeFalse();
});
