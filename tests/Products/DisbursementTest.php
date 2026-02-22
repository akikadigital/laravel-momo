<?php

namespace Akika\MoMo\Tests\Products;

use Akika\MoMo\Enums\Currency;
use Akika\MoMo\Products\Disbursement;
use Akika\MoMo\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DisbursementTest extends TestCase
{
    public string $env;

    public string $secondaryKey;

    public string $xReferenceId;

    public string $apiKey;

    public string $callbackHost;

    public string $baseMomoUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->env = $env = fake()->randomElement(['sandbox', 'production']);
        Config::set('momo.env', $env);
        Config::set("momo.{$env}.secondary_key", $this->secondaryKey = fake()->uuid());
        Config::set("momo.{$env}.user_reference_id", $this->xReferenceId = fake()->uuid());
        Config::set("momo.{$env}.api_key", $this->apiKey = fake()->uuid());
        Config::set('momo.provider_callback_host', $this->callbackHost = fake()->domainName());
        Config::set("momo.{$env}.base_url", $this->baseMomoUrl = fake()->url());
    }

    public function fakeAccessToken(): array
    {
        $path = Config::string('momo.disbursement.url_paths.create_access_token');
        $url = $this->baseMomoUrl.$path;

        $body = [
            'access_token' => fake()->uuid(),
            'token_type' => 'access_token',
            'expires_in' => 3600,
        ];

        Http::fake([
            $url => Http::response($body, 200),
        ]);

        return $body;
    }

    public function test_can_create_access_token(): void
    {
        $body = $this->fakeAccessToken();

        $response = (new Disbursement)->createAccessToken();

        $this->assertEquals($body, $response);
    }

    public function test_can_get_access_token(): void
    {
        $body = $this->fakeAccessToken();

        $token = (new Disbursement)->getAccessToken();

        $this->assertEquals($body['access_token'], $token);
    }

    public function test_can_initiate_a_transfer(): void
    {
        $this->fakeAccessToken();

        Config::set('momo.disbursement.callback_url', fake()->url());

        $path = Config::string('momo.disbursement.url_paths.transfer');
        $url = $this->baseMomoUrl.$path;

        Http::fake([
            $url => Http::response(null, 202),
        ]);

        $amount = fake()->randomFloat(2, 5, 10_000);
        $currency = fake()->randomElement(Currency::cases());
        $externalId = fake()->uuid();
        $payeeMsisdn = fake()->phoneNumber();
        $payerMessage = fake()->sentence();
        $payeeNote = fake()->sentence();

        $tReferenceId = (new Disbursement)->transfer(
            $amount,
            $currency,
            $externalId,
            $payeeMsisdn,
            $payerMessage,
            $payeeNote
        );

        $this->assertTrue(Str::isUuid($tReferenceId));
    }

    public function test_can_get_transfer_status(): void
    {
        $this->fakeAccessToken();

        $path = Config::string('momo.disbursement.url_paths.get_transfer_status');
        $url = $this->baseMomoUrl.$path;

        $body = [
            'amount' => fake()->randomNumber(),
            'currency' => fake()->randomElement(Currency::cases())->value,
            'financialTransactionId' => fake()->uuid(),
            'externalId' => fake()->uuid(),
            'payee' => [
                'partyIdType' => 'MSISDN',
                'partyId' => fake()->randomNumber(),
            ],
            'payerMessage' => fake()->sentence(),
            'payeeNote' => fake()->sentence(),
            'status' => 'SUCCESSFUL',
        ];

        $tReferenceId = fake()->uuid();
        $url = str_replace('{referenceId}', $tReferenceId, $url);

        Http::fake([
            $url => Http::response($body, 200),
        ]);

        $response = (new Disbursement)->getTransferStatus($tReferenceId);

        $this->assertEquals($body, $response);
    }

    public function test_can_get_account_balance(): void
    {
        $this->fakeAccessToken();

        $path = Config::string('momo.disbursement.url_paths.get_account_balance');
        $url = $this->baseMomoUrl.$path;

        $body = [
            'availableBalance' => strval(fake()->randomNumber(4)),
            'currency' => fake()->randomElement(Currency::cases())->value,
        ];

        Http::fake([
            $url => Http::response($body, 200),
        ]);

        $response = (new Disbursement)->getAccountBalance();

        $this->assertEquals($body, $response);
    }
}
