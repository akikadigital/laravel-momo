<?php

namespace Akika\MoMo\Tests\Config;

use Akika\MoMo\Config\MoMoConfig;
use Akika\MoMo\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use UnexpectedValueException;

class MoMoConfigTest extends TestCase
{
    public string $env;

    public string $secondaryKey;

    public string $xReferenceId;

    public string $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->env = $env = fake()->randomElement(['sandbox', 'production']);
        Config::set('momo.env', $env);
        Config::set("momo.{$env}.secondary_key", $this->secondaryKey = fake()->uuid());
        Config::set("momo.{$env}.user_reference_id", $this->xReferenceId = fake()->uuid());
        Config::set("momo.{$env}.api_key", $this->apiKey = fake()->uuid());
    }

    public function test_can_get_values_from_config(): void
    {
        $moMoConfig = new MoMoConfig;

        $this->assertEquals($this->secondaryKey, $moMoConfig->getSecondaryKey());
        $this->assertEquals($this->xReferenceId, $moMoConfig->getUserReferenceId());
        $this->assertEquals($this->apiKey, $moMoConfig->getApiKey());
    }

    public function test_can_get_values_passed_into_constructor(): void
    {
        $moMoConfig = new MoMoConfig(
            $secondaryKey = fake()->uuid(),
            $xReferenceId = fake()->uuid(),
            $apiKey = fake()->uuid(),
        );

        $this->assertEquals($secondaryKey, $moMoConfig->getSecondaryKey());
        $this->assertEquals($xReferenceId, $moMoConfig->getUserReferenceId());
        $this->assertEquals($apiKey, $moMoConfig->getApiKey());
    }

    public function test_throws_exception_if_api_key_not_found(): void
    {
        Config::set("momo.{$this->env}.api_key", null);

        $moMoConfig = new MoMoConfig(
            fake()->uuid(),
            fake()->uuid(),
        );

        $this->expectException(UnexpectedValueException::class);
        $moMoConfig->getApiKey();
    }
}
