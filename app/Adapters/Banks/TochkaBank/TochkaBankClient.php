<?php

namespace App\Adapters\Banks\TochkaBank;

use Psr\Log\LoggerInterface;
use TochkaApi\Auth\AccessToken;
use TochkaApi\Client as TochkaClient;
use TochkaApi\Exceptions\RequestException;
use TochkaApi\HttpAdapters\CurlHttpClient;

class TochkaBankClient extends \App\Adapters\Client
{

    public const CURRENCY = 'RUB';
    protected TochkaClient $api;

    public function __construct()
    {
        $this->initClient();
    }

    public function initClient(): void
    {
        $this->api = new TochkaClient(
            client_id: $this->getConfig('api.client_id'),
            client_secret: $this->getConfig('api.client_secret'),
            redirect_uri: $this->getConfig('api.redirect_uri'),
            adapter: new CurlHttpClient
        );

        //$this->api->setScopes($this->getConfig('account.scopes'));
        //$this->api->setPermissions($this->getConfig('account.permissions'));
        $this->api->setAccessToken(new AccessToken($this->getConfig('account.jwt')));
    }

    /**
     * @return void
     * @throws \TochkaApi\Exceptions\TochkaApiClientException
     */
    public function authorize(): void
    {
        $authorizeUrl = $this->api->authorize();
        redirect($authorizeUrl);
    }

    public function processAuthCode(string $code): void
    {
        $accessToken = $this->api->token($code);
        $this->api->setAccessToken($accessToken);
    }

    public function refreshToken(string $refreshToken): AccessToken
    {
        return $this->api->refreshToken($refreshToken);
    }

    protected function getConfig(string $string): mixed
    {
        return config($this->externalSystemName() . '.' . $string);
    }

    protected function getBaseHost(): string
    {
        $configValue = $this->getConfig('api.host');
        if (empty($configValue)) {
            throw new Exceptions\TochkaBankAdapterException('setup_the_client_config', ['config' => 'api.host']);
        }
        return $configValue;
    }

    protected function getMerchantId(): string
    {
        $configValue = $this->getConfig('account.merchant_id');
        if (empty($configValue)) {
            throw new Exceptions\TochkaBankAdapterException('setup_the_client_config', ['config' => 'account.merchant_id']);
        }
        return $configValue;
    }

    protected function getAccountId(): string
    {
        $configValue = $this->getConfig('account.account_id');
        if (empty($configValue)) {
            throw new Exceptions\TochkaBankAdapterException('setup_the_client_config', ['config' => 'account.legal_id']);
        }
        return $configValue;
    }

    public function externalSystemName(): string
    {
        return 'tochkabank';
    }

    protected function customRequest(string $method, string $url, array $data = [], int $try = 1): array
    {
        $logPayload = [
            'url' => $url,
            'method' => $method,
            'data' => $data,
            'try' => $try,
        ];

        $this->getLogger()->debug('TochkaBankClient: performing custom request #' . $try, $logPayload);

        try {
            $result = $this->api->custom()->request($method, $url, $data);
        } catch (\Throwable $e) {

            $jsonMessage = json_decode($e->getMessage());
            $errorMessage = $jsonMessage?->message ?? $e->getMessage();
            $code = $e->getCode();
            $maxTries = $this->getConfig('api.max_retries');
            $logPayload += [
                'response' => (array)$jsonMessage,
                'error' => $errorMessage,
                'code' => $code,
            ];

            if ($e instanceof  RequestException) {
                $logPayload['details'] = $jsonMessage->Errors ?? null;
            }

            $this->logException('TochkaBankClient: custom request error', $logPayload);

            if (in_array($code, [400, 404], true) && $try < $maxTries) {
                usleep(500);
                return $this->customRequest($method, $url, $data, $try + 1);
            }

            throw new Exceptions\TochkaBankAdapterException(
                $errorMessage . (in_array($code, [403, 404], true) ? ' (Check the JWT)' : ''),
                $logPayload,
                $code
            );
        }

        $this->getLogger()->debug('TochkaBankClient: custom request result', $logPayload + [
            'result' => $result,
        ]);

        if ($result === null) {
            throw new Exceptions\TochkaBankAdapterException('Empty response');
        }

        if (!array_key_exists('Data', $result)) {
            throw new Exceptions\TochkaBankAdapterException('Invalid response');
        }

        return $result['Data'];
    }

    protected function getLogger(): LoggerInterface
    {
        return app(LoggerInterface::class);
    }

    protected function logException(string $message, array $logPayload): void
    {
        $this->getLogger()->error($message, $logPayload);
    }
}