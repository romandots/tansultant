<?php
namespace App\Checks;

use Socket\Raw\Factory as SocketFactory;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class ReverbCheck extends Check
{
    protected int $port;

    public function __construct(int $port)
    {
        $this->port = $port;
    }

    public static function new(int $port = 8080): static
    {
        return new self($port);
    }

    public function run(): Result
    {
        $result = Result::make('Reverb is running');
        $result->shortSummary('Check if Reverb server is serving connections');

        try {
            $socket = (new SocketFactory())->createClient("tcp://localhost:{$this->port}", 1);
            $socket->close();

            return $result->ok();
        } catch (\Exception $e) {
            return $result->failed("Could not connect to port {$this->port}: " . $e->getMessage());
        }
    }
}
