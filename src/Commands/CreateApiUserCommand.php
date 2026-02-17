<?php

namespace Akika\MoMo\Commands;

use Akika\MoMo\Actions\CreateApiUserAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CreateApiUserCommand extends Command
{
    public $signature = 'momo:create-api-user {-Y|no-confirmation}';

    public $description = 'Create an API User using the configured Reference Id in the .env file.';

    public function handle(): int
    {
        /** @var bool */
        $requiresConfirmation = $this->hasOption('no-confirmation');

        $env = Config::string('momo.env');
        $secondaryKey = Config::string("momo.{$env}.secondary_key");
        $xReferenceId = Config::string("momo.{$env}.user_reference_id");

        $this->line('Creating an API User:');
        $this->line("env: {$env}");
        $this->line("Secondary Key: {$secondaryKey}");
        $this->line("User Reference ID: {$xReferenceId}");

        if ($requiresConfirmation && ! $this->confirm('Proceed?')) {
            return self::SUCCESS;
        }

        (new CreateApiUserAction)();

        return self::SUCCESS;
    }
}
