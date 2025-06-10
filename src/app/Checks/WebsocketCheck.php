<?php
namespace App\Checks;

use Socket\Raw\Factory as SocketFactory;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class WebsocketCheck extends Check
{
    protected int $port;

    public function __construct(int $port)
    {
        $this->port = $port;
    }

    public static function new(int $port = 6001): static
    {
        return new self($port);
    }

    public function run(): Result
    {
        $result = Result::make('Websocket is running');
        $result->shortSummary('Check if websocket server is serving connections');

        try {
            $socket = (new SocketFactory())->createClient("tcp://localhost:{$this->port}", 1);
            $socket->close();

            return $result->ok();
        } catch (\Exception $e) {
            return $result->failed("Could not connect to port {$this->port}: " . $e->getMessage());
        }
    }
}