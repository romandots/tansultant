<?php

namespace App\Adapters\Telegram\Transport;

use App\Adapters\Telegram\TelegramClientException;
use Illuminate\Support\Facades\Http;

class TelegramAdapterHttp implements TelegramAdapterTransport
{

    protected \Illuminate\Http\Client\PendingRequest $http;

    public function __construct()
    {
        $apiHost = config('telegram.api_host');
        $apiToken = config('telegram.api_key');

        if (!$apiHost || !$apiToken) {
            throw new \Exception('Telegram API host or token is not set');
        }

        $this->http = Http::baseUrl($apiHost)
            ->withHeaders([
                'API_KEY' => $apiToken,
                'Content-Type' => 'application/json',
            ]);
    }

    public function ping(): bool
    {
        return $this->http->get(config('telegram.endpoints.ping', '/ping'))->throwUnlessStatus(200)->ok();
    }

    public function sendMessage(string $phone, string $message): void
    {
        $response = $this->http
            ->post(config('telegram.endpoints.send_message', '/message'), [
                'phone' => $phone,
                'message' => $message,
            ])
            ->throwUnlessStatus(200);
    }
}