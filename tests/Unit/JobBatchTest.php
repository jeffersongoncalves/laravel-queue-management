<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use JeffersonGoncalves\QueueManagement\Models\JobBatch;

function createJobBatch(array $overrides = []): JobBatch
{
    $id = $overrides['id'] ?? (string) Str::uuid();
    unset($overrides['id']);

    $batch = new JobBatch(array_merge([
        'name' => 'Example batch',
        'total_jobs' => 10,
        'pending_jobs' => 5,
        'failed_jobs' => 0,
        'failed_job_ids' => [],
    ], $overrides));

    // "id" is guarded, so assign it outside of mass assignment.
    $batch->id = $id;
    $batch->save();

    return $batch;
}

it('reads the table name from config', function () {
    config()->set('queue-management.tables.job_batches', 'custom_job_batches');

    expect((new JobBatch)->getTable())->toBe('custom_job_batches');
});

it('uses a non-incrementing string key', function () {
    $batch = new JobBatch;

    expect($batch->getKeyType())->toBe('string')
        ->and($batch->getIncrementing())->toBeFalse();
});

it('stores options transparently with base64 encoding', function () {
    $batch = createJobBatch(['options' => 'plain-options-value']);

    $stored = $batch->getRawOriginal('options');

    expect($stored)->toBe(base64_encode('plain-options-value'))
        ->and($batch->refresh()->options)->toBe('plain-options-value');
});

it('casts failed_job_ids to an array', function () {
    $batch = createJobBatch(['failed_job_ids' => [1, 2, 3]])->refresh();

    expect($batch->failed_job_ids)->toBe([1, 2, 3]);
});

it('auto-sets created_at on save', function () {
    Carbon::setTestNow(Carbon::createFromTimestamp(1_700_000_000));

    $batch = createJobBatch();

    expect($batch->created_at)->toBe(1_700_000_000);

    Carbon::setTestNow();
});
