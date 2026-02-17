<?php

namespace Akika\MoMo\Commands;

use Akika\MoMo\Actions\CreateApiUserAction;
use Akika\MoMo\Config\MoMoConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CreateApiUserCommand extends Command
{
    public $signature = 'momo:create-api-user
                         {--Y|no-confirmation : Proceed without asking for confirmation}
                         {--secondary-key= : If not provided, this will pick from your .env config.}
                         {--user-reference-id= : If not provided, this will pick from your .env config.}';

    public $description = 'Create an API User using the configured Reference Id in the .env file.';

    public function handle(): int
    {
        /** @var bool */
        $noConfirmation = $this->option('no-confirmation');
        /** @var ?string */
        $secondaryKey = $this->option('secondary-key');
        /** @var ?string */
        $xReferenceId = $this->option('user-reference-id');

        $env = Config::string('momo.env');
        // TODO: Test this null checking
        $secondaryKey ??= Config::string("momo.{$env}.secondary_key");
        $xReferenceId ??= Config::string("momo.{$env}.user_reference_id");

        $this->line('Creating an API User:');
        $this->line("env: {$env}");
        $this->line("Secondary Key: {$secondaryKey}");
        $this->line("User Reference ID: {$xReferenceId}");

        if (! $noConfirmation && ! $this->confirm('Proceed?')) {
            return self::FAILURE;
        }

        $moMoConfig = new MoMoConfig(
            $secondaryKey,
            $xReferenceId,
        );

        (new CreateApiUserAction($moMoConfig))();

        return self::SUCCESS;
    }
}
