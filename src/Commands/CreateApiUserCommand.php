<?php

namespace Akika\MoMo\Commands;

use Akika\MoMo\Actions\CreateApiUserAction;
use Illuminate\Console\Command;

class CreateApiUserCommand extends Command
{
    public $signature = 'momo:create-api-user';

    public $description = 'Create an API User using the configured Reference Id in the .env file.';

    public function handle(): int
    {
        (new CreateApiUserAction)();

        return self::SUCCESS;
    }
}
