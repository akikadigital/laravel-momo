<?php

namespace Akika\MoMo\Commands;

use Akika\MoMo\Actions\CreateApiKeyAction;
use Akika\MoMo\Config\MoMoConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

use function Laravel\Prompts\text;

class CreateApiKeyCommand extends Command
{
    public $signature = 'momo:create-api-key
                         {--Y|no-confirmation : Proceed without asking for confirmation}
                         {--secondary-key= : If not provided, this will pick from your .env config.}
                         {--user-reference-id= : (uuid V4) If not provided, this will pick from your .env config.}';

    public $description = 'Create an API Key using the configured Secondary Key and Reference Id.';

    public function handle(): int
    {
        $moMoConfig = $this->getMoMoConfig();

        if (! $this->confirmed($moMoConfig)) {
            return self::FAILURE;
        }

        $apiKey = (new CreateApiKeyAction($moMoConfig))();

        $this->info('API key generated successfully. Add this API key to your .env file:');

        $envKey = 'MOMO_'.strtoupper(Config::string('momo.env')).'_API_KEY';
        $this->line("    {$envKey}={$apiKey}");

        return self::SUCCESS;
    }

    public function getMoMoConfig(): MoMoConfig
    {
        /** @var bool */
        $noConfirmation = $this->option('no-confirmation');
        /** @var ?string */
        $secondaryKey = $this->option('secondary-key');
        /** @var ?string */
        $xReferenceId = $this->option('user-reference-id');

        $env = Config::string('momo.env');

        if ($noConfirmation) {
            $secondaryKey ??= Config::string("momo.{$env}.secondary_key");
            $xReferenceId ??= Config::string("momo.{$env}.user_reference_id");
        } else {
            $secondaryKey ??= text(label: 'Enter the Secondary Key:',
                default: Config::string("momo.{$env}.secondary_key"),
                required: true,
            );
            $xReferenceId ??= text(label: 'Enter the User Reference ID:',
                default: Config::string("momo.{$env}.user_reference_id"),
                required: true,
                hint: 'Use a valid UUIDv4',
            );
        }

        return new MoMoConfig(
            $secondaryKey,
            $xReferenceId,
        );
    }

    public function confirmed(MoMoConfig $moMoConfig): bool
    {
        $env = Config::string('momo.env');
        $this->line('Creating an API Key:');
        $this->line("    env: {$env}");
        $this->line('    Secondary Key: '.$moMoConfig->getSecondaryKey());
        $this->line('    User Reference ID: '.$moMoConfig->getUserReferenceId());

        /** @var bool */
        $noConfirmation = $this->option('no-confirmation');

        if ($noConfirmation) {
            return true;
        }

        return $this->confirm('Proceed?');
    }
}
