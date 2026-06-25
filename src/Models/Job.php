<?php

namespace JeffersonGoncalves\QueueManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $queue
 * @property array $payload
 * @property int $attempts
 * @property int|null $reserved_at
 * @property int $available_at
 * @property int $created_at
 * @property-read string $displayName
 * @property-read int $maxTries
 * @property-read int $delay
 */
class Job extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['displayName', 'maxTries', 'delay'];

    protected $casts = [
        'payload' => 'array',
    ];

    public $timestamps = false;

    public function getTable(): string
    {
        return config('queue-management.tables.jobs') ?? parent::getTable();
    }

    protected static function booted(): void
    {
        static::saving(function (Job $job): void {
            if (empty($job->available_at)) {
                $job->available_at = Carbon::now()->timestamp;
            }

            if (empty($job->created_at)) {
                $job->created_at = Carbon::now()->timestamp;
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
