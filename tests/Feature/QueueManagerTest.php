<?php

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use JeffersonGoncalves\QueueManagement\Facades\QueueManagement;
use JeffersonGoncalves\QueueManagement\Models\FailedJob;
use JeffersonGoncalves\QueueManagement\Models\Job;
use JeffersonGoncalves\QueueManagement\QueueManager;
use Mockery\MockInterface;

function fakeKernel(): MockInterface
{
    $kernel = Mockery::mock(ConsoleKernel::class);
    Artisan::swap($kernel);

    return $kernel;
}

function makeFailedJob(string $uuid): FailedJob
{
    return FailedJob::query()->create([
        'uuid' => $uuid,
        'connection' => 'database',
        'queue' => 'default',
        'payload' => [],
        'exception' => 'boom',
    ]);
}

it('resolves the service as a singleton', function () {
    expect(app(QueueManager::class))->toBe(app(QueueManager::class));
});

it('retries using the integer id when the driver is database', function () {
    config()->set('queue.failed.driver', 'database');

    $failedJob = makeFailedJob((string) Str::uuid());

    $kernel = fakeKernel();
    $kernel->shouldReceive('call')
        ->once()
        ->with('queue:retry', ['id' => $failedJob->id])
        ->andReturn(0);

    app(QueueManager::class)->retry($failedJob->id);
});

it('retries using the uuid when the driver is database-uuids', function () {
    config()->set('queue.failed.driver', 'database-uuids');

    $uuid = (string) Str::uuid();
    $failedJob = makeFailedJob($uuid);

    $kernel = fakeKernel();
    $kernel->shouldReceive('call')
        ->once()
        ->with('queue:retry', ['id' => $uuid])
        ->andReturn(0);

    app(QueueManager::class)->retry($failedJob->id);
});

it('retries all failed jobs', function () {
    $kernel = fakeKernel();
    $kernel->shouldReceive('call')
        ->once()
        ->with('queue:retry', ['id' => ['all']])
        ->andReturn(0);

    app(QueueManager::class)->retryAll();
});

it('forgets a single failed job', function () {
    $kernel = fakeKernel();
    $kernel->shouldReceive('call')
        ->once()
        ->with('queue:forget', ['id' => 42])
        ->andReturn(0);

    app(QueueManager::class)->forget(42);
});

it('flushes all failed jobs', function () {
    $kernel = fakeKernel();
    $kernel->shouldReceive('call')
        ->once()
        ->with('queue:flush')
        ->andReturn(0);

    app(QueueManager::class)->flush();
});

it('deletes a pending job by id', function () {
    $job = Job::query()->create([
        'queue' => 'default',
        'payload' => [],
        'attempts' => 0,
    ]);

    app(QueueManager::class)->deletePendingJob($job->id);

    expect(Job::query()->whereKey($job->id)->exists())->toBeFalse();
});

it('exposes the manager through the facade', function () {
    $kernel = fakeKernel();
    $kernel->shouldReceive('call')
        ->once()
        ->with('queue:flush')
        ->andReturn(0);

    QueueManagement::flush();
});
