<?php

namespace Akika\LaravelMomo\Commands;

use Illuminate\Console\Command;

class LaravelMomoCommand extends Command
{
    public $signature = 'laravel-momo';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
