# Changelog

All notable changes to `laravel-queue-management` will be documented in this file.

## 1.0.1 - 2026-09-30

### Fixed

- `forget()` now resolves the failed job's uuid when `queue.failed.driver` is `database-uuids`. Previously it passed the integer id to `queue:forget`, which threw on MySQL and silently deleted nothing on other databases (#10, #11).
- uuid inputs to `forget()`/`retry()` are passed through unchanged instead of being looked up by primary key, which on MySQL could resolve another failed job (#11).

## 1.0.0 - 2026-06-25

Initial release. Eloquent models for Laravel's database queue tables (jobs, failed_jobs, job_batches) plus a QueueManager service and facade to retry, forget and flush failed jobs and delete pending jobs.
