<?php

namespace Akika\MoMo\Commands;

use Akika\MoMo\Actions\CreateApiUserAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CreateApiUserCommand extends Command
{
    public $signature = 'momo:create-api-user {--Y|no-confirmation}';

    public $description = 'Create an API User using the configured Reference Id in the .env file.';

    public function handle(): int
    {
        /** @var bool */
        $noConfirmation = $this->option('no-confirmation');

        $env = Config::string('momo.env');
        $secondaryKey = Config::string("momo.{$env}.secondary_key");
        $xReferenceId = Config::string("momo.{$env}.user_reference_id");

        $this->line('Creating an API User:');
        $this->line("env: {$env}");
        $this->line("Secondary Key: {$secondaryKey}");
        $this->line("User Reference ID: {$xReferenceId}");

        if (! $noConfirmation && ! $this->confirm('Proceed?')) {
            return self::FAILURE;
        }

        (new CreateApiUserAction)();

        return self::SUCCESS;
    }
}
