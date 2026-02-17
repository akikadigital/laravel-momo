<?php

namespace Akika\MoMo\Tests\Commands;

use Akika\MoMo\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CreateApiUserCommandTest extends TestCase
{
    public string $env;

    public string $secondaryKey;

    public string $xReferenceId;

    public string $callbackHost;

    public string $url;

    protected function setUp(): void
    {
        parent::setUp();

        $this->env = $env = fake()->randomElement(['sandbox', 'production']);
        Config::set('momo.env', $env);
        Config::set("momo.{$env}.secondary_key", $this->secondaryKey = fake()->uuid());
        Config::set("momo.{$env}.user_reference_id", $this->xReferenceId = fake()->uuid());
        Config::set('momo.provider_callback_host', $this->callbackHost = fake()->domainName());
        Config::set("momo.{$env}.base_url", $baseUrl = fake()->url());

        $path = Config::string('momo.url_paths.create_api_user');
        $this->url = $baseUrl.$path;

        Http::fake([
            $this->url => Http::response('', 201),
        ]);
    }

    public function test_can_create_api_user_via_cli(): void
    {
        $this->artisan('momo:create-api-user -Y')
            ->expectsOutput('Creating an API User:')
            ->expectsOutput("env: {$this->env}")
            ->expectsOutput("Secondary Key: {$this->secondaryKey}")
            ->expectsOutput("User Reference ID: {$this->xReferenceId}")
            ->assertSuccessful();
    }

    public function test_can_create_api_user_with_confirmation(): void
    {
        $this->artisan('momo:create-api-user')
            ->expectsOutput('Creating an API User:')
            ->expectsOutput("env: {$this->env}")
            ->expectsOutput("Secondary Key: {$this->secondaryKey}")
            ->expectsOutput("User Reference ID: {$this->xReferenceId}")
            ->expectsConfirmation('Proceed?', 'yes')
            ->assertSuccessful();
    }

    public function test_does_not_create_api_user_without_confirmation(): void
    {
        $this->artisan('momo:create-api-user')
            ->expectsOutput('Creating an API User:')
            ->expectsOutput("env: {$this->env}")
            ->expectsOutput("Secondary Key: {$this->secondaryKey}")
            ->expectsOutput("User Reference ID: {$this->xReferenceId}")
            ->expectsConfirmation('Proceed?', 'no')
            ->assertFailed();
    }
}
