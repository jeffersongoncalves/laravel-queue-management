<?php

namespace JeffersonGoncalves\QueueManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property int $total_jobs
 * @property int $pending_jobs
 * @property int $failed_jobs
 * @property array $failed_job_ids
 * @property string|null $options
 * @property int|null $cancelled_at
 * @property int $created_at
 * @property int|null $finished_at
 */
class JobBatch extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'failed_job_ids' => 'array',
    ];

    public $timestamps = false;

    public function getTable(): string
    {
        return config('queue-management.tables.job_batches') ?? parent::getTable();
    }

    protected static function booted(): void
    {
        static::saving(function (JobBatch $jobBatch): void {
            if (empty($jobBatch->created_at)) {
                $jobBatch->created_at = Carbon::now()->timestamp;
            }
        });
    }

    public function setOptionsAttribute(mixed $value): void
    {
        $this->attributes['options'] = base64_encode((string) $value);
    }

    public function getOptionsAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return base64_decode($value);
    }
}
