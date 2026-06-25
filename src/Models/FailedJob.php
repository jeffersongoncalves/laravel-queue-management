<?php

namespace JeffersonGoncalves\QueueManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property string $connection
 * @property string $queue
 * @property array $payload
 * @property string $exception
 * @property Carbon $failed_at
 * @property-read string $displayName
 * @property-read int $maxTries
 * @property-read int $delay
 */
class FailedJob extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['displayName', 'maxTries', 'delay'];

    protected $casts = [
        'payload' => 'array',
        'failed_at' => 'datetime',
    ];

    public $timestamps = false;

    public function getTable(): string
    {
        return config('queue-management.tables.failed_jobs') ?? parent::getTable();
    }

    protected static function booted(): void
    {
        static::saving(function (FailedJob $failedJob): void {
            if (empty($failedJob->failed_at)) {
                $failedJob->failed_at = Carbon::now();
            }
        });
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->payload['displayName'] ?? '';
    }

    public function getMaxTriesAttribute(): int
    {
        return $this->payload['maxTries'] ?? 0;
    }

    public function getDelayAttribute(): int
    {
        return $this->payload['delay'] ?? 0;
    }
}
