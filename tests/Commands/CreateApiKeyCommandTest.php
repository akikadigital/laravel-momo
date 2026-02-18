<?php

namespace Akika\MoMo\Tests\Commands;

use Akika\MoMo\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CreateApiKeyCommandTest extends TestCase
{
    public string $env;

    public string $secondaryKey;

    public string $xReferenceId;

    public string $callbackHost;

    public string $url;

    public string $envKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->env = $env = fake()->randomElement(['sandbox', 'production']);
        Config::set('momo.env', $env);
        Config::set("momo.{$env}.secondary_key", $this->secondaryKey = fake()->uuid());
        Config::set("momo.{$env}.user_reference_id", $this->xReferenceId = fake()->uuid());
        Config::set('momo.provider_callback_host', $this->callbackHost = fake()->domainName());
        Config::set("momo.{$env}.base_url", $baseUrl = fake()->url());

        $path = Config::string('momo.url_paths.create_api_key');
        $this->url = $baseUrl.$path;

        $this->envKey = 'MOMO_'.strtoupper($env).'_API_KEY';
    }

    public function test_can_create_user_without_confirmation_using_env(): void
    {
        $apiKey = fake()->uuid();
        $this->url = str_replace('{referenceId}', $this->xReferenceId, $this->url);
        Http::fake([
            $this->url => Http::response(['apiKey' => $apiKey], 201),
        ]);

        $this->artisan('momo:create-api-key -Y')
            ->expectsOutput('Creating an API Key:')
            ->expectsOutput("env: {$this->env}")
            ->expectsOutput("Secondary Key: {$this->secondaryKey}")
            ->expectsOutput("User Reference ID: {$this->xReferenceId}")
            ->expectsOutput('API key generated successfully. Add this API key to your .env file:')
            ->expectsOutput("\t{$this->envKey}={$apiKey}")
            ->assertSuccessful();
    }

    public function test_can_create_user_without_confirmation_using_passed_options(): void
    {
        $params = [
            '-Y' => true,
            '--secondary-key' => $secondaryKey = fake()->uuid(),
            '--user-reference-id' => $xReferenceId = fake()->uuid(),
        ];

        $apiKey = fake()->uuid();
        $this->url = str_replace('{referenceId}', $xReferenceId, $this->url);
        Http::fake([
            $this->url => Http::response(['apiKey' => $apiKey], 201),
        ]);

        $this->artisan('momo:create-api-key', $params)
            ->expectsOutput('Creating an API Key:')
            ->expectsOutput("env: {$this->env}")
            ->expectsOutput("Secondary Key: {$secondaryKey}")
            ->expectsOutput("User Reference ID: {$xReferenceId}")
            ->expectsOutput('API key generated successfully. Add this API key to your .env file:')
            ->expectsOutput("\t{$this->envKey}={$apiKey}")
            ->assertSuccessful();
    }

    public function test_can_create_user_with_confirmation_using_passed_options(): void
    {
        $params = [
            '--secondary-key' => $secondaryKey = fake()->uuid(),
            '--user-reference-id' => $xReferenceId = fake()->uuid(),
        ];

        $apiKey = fake()->uuid();
        $this->url = str_replace('{referenceId}', $xReferenceId, $this->url);
        Http::fake([
            $this->url => Http::response(['apiKey' => $apiKey], 201),
        ]);

        $this->artisan('momo:create-api-key', $params)
            ->expectsOutput('Creating an API Key:')
            ->expectsOutput("env: {$this->env}")
            ->expectsOutput("Secondary Key: {$secondaryKey}")
            ->expectsOutput("User Reference ID: {$xReferenceId}")
            ->expectsConfirmation('Proceed?', 'yes')
            ->expectsOutput('API key generated successfully. Add this API key to your .env file:')
            ->expectsOutput("\t{$this->envKey}={$apiKey}")
            ->assertSuccessful();
    }

    public function test_fails_if_confirmation_is_needed_but_is_not_given(): void
    {
        $params = [
            '--secondary-key' => $secondaryKey = fake()->uuid(),
            '--user-reference-id' => $xReferenceId = fake()->uuid(),
        ];

        $this->artisan('momo:create-api-key', $params)
            ->expectsOutput('Creating an API Key:')
            ->expectsOutput("env: {$this->env}")
            ->expectsOutput("Secondary Key: {$secondaryKey}")
            ->expectsOutput("User Reference ID: {$xReferenceId}")
            ->expectsConfirmation('Proceed?', 'no')
            ->assertFailed();
    }

    public function test_can_create_user_with_confirmation_using_text_input(): void
    {
        $secondaryKey = fake()->uuid();
        $xReferenceId = fake()->uuid();

        $apiKey = fake()->uuid();
        $this->url = str_replace('{referenceId}', $xReferenceId, $this->url);
        Http::fake([
            $this->url => Http::response(['apiKey' => $apiKey], 201),
        ]);

        $this->artisan('momo:create-api-key')
            ->expectsOutput('Creating an API Key:')
            ->expectsQuestion('Enter the Secondary Key?', $secondaryKey)
            ->expectsQuestion('Enter the User Reference ID?', $xReferenceId)
            ->expectsOutput("env: {$this->env}")
            ->expectsOutput("Secondary Key: {$secondaryKey}")
            ->expectsOutput("User Reference ID: {$xReferenceId}")
            ->expectsConfirmation('Proceed?', 'yes')
            ->expectsOutput('API key generated successfully. Add this API key to your .env file:')
            ->expectsOutput("\t{$this->envKey}={$apiKey}")
            ->assertSuccessful();
    }
}
