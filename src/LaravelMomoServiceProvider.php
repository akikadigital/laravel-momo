<?php

namespace Akika\LaravelMomo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Akika\LaravelMomo\Commands\LaravelMomoCommand;

class LaravelMomoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-momo')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_momo_table')
            ->hasCommand(LaravelMomoCommand::class);
    }
}
