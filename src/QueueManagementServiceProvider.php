<?php

namespace JeffersonGoncalves\QueueManagement;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class QueueManagementServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('queue-management')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(QueueManager::class);
    }
}
