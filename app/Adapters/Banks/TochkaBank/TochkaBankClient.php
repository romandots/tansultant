<?php

namespace App\Adapters\Banks\TochkaBank;

use TochkaApi\Auth\AccessToken;
use TochkaApi\Client as TochkaClient;
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

        $this->api->setScopes($this->getConfig('account.scopes'));
        $this->api->setPermissions($this->getConfig('account.permissions'));
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
        return config($this->getConfigName() . '.' . $string);
    }

    protected function getConfigName(): string
    {
        return 'tochka';
    }

    protected function getBaseHost(): string
    {
        return $this->getConfig('api.host');
    }

    protected function getMerchantId(): string
    {
        return $this->getConfig('account.merchant_id');
    }

    protected function getAccountId(): string
    {
        return $this->getConfig('account.account_id');
    }
}