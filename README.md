<div class="filament-hidden">

![Laravel Queue Management](https://raw.githubusercontent.com/jeffersongoncalves/laravel-queue-management/master/art/jeffersongoncalves-laravel-queue-management.png)

</div>

# Laravel Queue Management

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-queue-management.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-queue-management)
[![run-tests](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-queue-management/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/laravel-queue-management/actions/workflows/run-tests.yml)
[![Fix PHP code style issues](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-queue-management/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-queue-management/actions/workflows/fix-php-code-style-issues.yml)
[![PHPStan](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-queue-management/phpstan.yml?branch=master&label=phpstan&style=flat-square)](https://github.com/jeffersongoncalves/laravel-queue-management/actions/workflows/phpstan.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-queue-management.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-queue-management)

A headless toolkit for managing Laravel's database-driver queue tables (`jobs`, `failed_jobs`, `job_batches`). It provides Eloquent models for each table plus a small service layer (and facade) to retry, forget and flush failed jobs and delete pending jobs.

It contains **no UI framework of its own**. UI plugins (such as a Filament plugin) can depend on this package for the underlying models and operations.

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13

## Installation

```bash
composer require jeffersongoncalves/laravel-queue-management
```

The package uses Laravel's auto-discovery, so the service provider and the `QueueManagement` facade are registered automatically.

### Publish Configuration (optional)

```bash
php artisan vendor:publish --tag=queue-management-config
```

This package reads from Laravel's existing queue tables. It does not ship migrations — make sure your application has run Laravel's built-in queue migrations (`jobs`, `failed_jobs`, `job_batches`).

## Configuration

The configuration file (`config/queue-management.php`) lets you map the queue table names if your application uses custom ones:

```php
return [
    'tables' => [
        'jobs' => 'jobs',
        'failed_jobs' => 'failed_jobs',
        'job_batches' => 'job_batches',
    ],
];
```

## Usage

### Models

```php
use JeffersonGoncalves\QueueManagement\Models\Job;
use JeffersonGoncalves\QueueManagement\Models\FailedJob;
use JeffersonGoncalves\QueueManagement\Models\JobBatch;

// Pending jobs
$pending = Job::query()->get();
$first = $pending->first();
$first->displayName; // e.g. "App\Jobs\SendEmail"
$first->maxTries;    // int
$first->delay;       // int

// Failed jobs
$failed = FailedJob::query()->get();
$failed->first()->failed_at; // Carbon instance

// Job batches (options are transparently base64 decoded)
$batches = JobBatch::query()->get();
$batches->first()->options;
```

### Service layer

Resolve the `QueueManager` from the container:

```php
use JeffersonGoncalves\QueueManagement\QueueManager;

$manager = app(QueueManager::class);

// Retry one or more failed jobs (by failed_jobs.id).
// When queue.failed.driver is "database-uuids" the uuid is used automatically.
$manager->retry(1, 2, 3);

// Retry every failed job
$manager->retryAll();

// Forget a single failed job by id or uuid
$manager->forget(1);

// Flush all failed jobs
$manager->flush();

// Delete a pending job from the jobs table by id
$manager->deletePendingJob(10);
```

### Facade

```php
use JeffersonGoncalves\QueueManagement\Facades\QueueManagement;

QueueManagement::retry(1, 2, 3);
QueueManagement::retryAll();
QueueManagement::forget(1);
QueueManagement::flush();
QueueManagement::deletePendingJob(10);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
