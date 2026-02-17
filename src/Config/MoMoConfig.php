<?php

namespace Akika\MoMo\Config;

use Illuminate\Support\Facades\Config;
use UnexpectedValueException;

class MoMoConfig
{
    private readonly string $env;

    public function __construct(
        private readonly ?string $secondaryKey = null,
        private readonly ?string $userReferenceId = null,
        private ?string $apiKey = null,
    ) {
        $this->env = Config::string('momo.env');
    }

    public function getSecondaryKey(): string
    {
        return $this->secondaryKey ?? Config::string("momo.{$this->env}.secondary_key");
    }

    public function getUserReferenceId(): string
    {
        return $this->userReferenceId ?? Config::string("momo.{$this->env}.user_reference_id");
    }

    public function getApiKey(): string
    {
        /** @var ?string */
        $configApiKey = config("momo.{$this->env}.api_key");
        $this->apiKey ??= $configApiKey;

        if (! $this->apiKey) {
            throw new UnexpectedValueException("No value found for 'momo.{$this->env}.api_key'.");
        }

        return $this->apiKey;
    }
}
