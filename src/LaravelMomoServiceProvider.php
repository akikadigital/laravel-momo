<?php

namespace Akika\LaravelMomo;

use Akika\LaravelMomo\Commands\LaravelMomoCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasCommand(LaravelMomoCommand::class);
    }
}
