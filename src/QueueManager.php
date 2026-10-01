<?php

namespace JeffersonGoncalves\QueueManagement;

use Illuminate\Support\Facades\Artisan;
use JeffersonGoncalves\QueueManagement\Models\FailedJob;
use JeffersonGoncalves\QueueManagement\Models\Job;

class QueueManager
{
    /**
     * Retry the given failed jobs.
     *
     * Detects whether the failed job driver expects a UUID or an integer id:
     * when "queue.failed.driver" is "database-uuids" the failed job's uuid is
     * passed to the command, otherwise the id is used. The "queue:retry" artisan
     * command is called once per identifier, mirroring the behaviour of the
     * original Nova Retry action which dispatches the command per model.
     */
    public function retry(string|int ...$ids): void
    {
        foreach ($ids as $id) {
            Artisan::call('queue:retry', ['id' => $this->failedJobIdentifier($id)]);
        }
    }

    /**
     * Retry all failed jobs.
     */
    public function retryAll(): void
    {
        Artisan::call('queue:retry', ['id' => ['all']]);
    }

    /**
     * Forget (delete) a single failed job by its id or uuid.
     */
    public function forget(string|int $id): void
    {
        Artisan::call('queue:forget', ['id' => $this->failedJobIdentifier($id)]);
    }

    /**
     * Flush (delete) all failed jobs.
     */
    public function flush(): void
    {
        Artisan::call('queue:flush');
    }

    /**
     * Delete a pending job from the jobs table by its id.
     */
    public function deletePendingJob(int $id): void
    {
        Job::query()->whereKey($id)->delete();
    }

    /**
     * Resolve the identifier the failed job provider expects: the uuid when
     * "queue.failed.driver" is "database-uuids", otherwise the given id.
     * Non-numeric input is already a uuid and is returned untouched: MySQL
     * would cast "7f3a..." to 7 and resolve another job's uuid.
     */
    private function failedJobIdentifier(string|int $id): string|int
    {
        if (config('queue.failed.driver') !== 'database-uuids' || ! ctype_digit((string) $id)) {
            return $id;
        }

        return FailedJob::query()->whereKey($id)->value('uuid') ?? $id;
    }
}
