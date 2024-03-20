<?php

namespace App\Adapters\Telegram;

use App\Adapters\Client;
use Illuminate\Support\Facades\Http;

class TelegramClient extends Client
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

    public function externalSystemName(): string
    {
        return 'telegram';
    }

    public function ping(): bool
    {
        try {
            return $this->http->get(config('telegram.endpoints.ping', '/ping'))->ok();
        } catch (\Exception $e) {
            throw new TelegramClientException('Telegram server is not responding: ' .$e->getMessage(), [], 500);
        }
    }

    public function sendMessage(string $phone, string $message): bool
    {
        try {
            $response = $this->http
                ->post(config('telegram.endpoints.send_message', '/message'), [
                    'phone' => $phone,
                    'message' => $message,
                ])
                ->throwIfStatus(401);

            return $response->ok();
        } catch (\Exception $e) {
            throw new TelegramClientException('Message not sent: '. $e->getMessage(), [], 500);
        }
    }

}