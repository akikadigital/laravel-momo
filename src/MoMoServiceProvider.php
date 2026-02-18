<?php

namespace Akika\MoMo;

use Akika\MoMo\Commands\CreateApiKeyCommand;
use Akika\MoMo\Commands\CreateApiUserCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MoMoServiceProvider extends PackageServiceProvider
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
            ->hasCommand(CreateApiUserCommand::class)
            ->hasCommand(CreateApiKeyCommand::class);
    }
}
