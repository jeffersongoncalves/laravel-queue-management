<?php

namespace JeffersonGoncalves\QueueManagement\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\QueueManagement\QueueManager;

/**
 * @method static void retry(string|int ...$ids)
 * @method static void retryAll()
 * @method static void forget(string|int $id)
 * @method static void flush()
 * @method static void deletePendingJob(int $id)
 *
 * @see QueueManager
 */
class QueueManagement extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return QueueManager::class;
    }
}
