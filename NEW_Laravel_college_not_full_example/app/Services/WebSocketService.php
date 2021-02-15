<?php

namespace App\Services;

use Ratchet\Client\Connector;
use React\EventLoop\Factory as ReactFactory;
use React\EventLoop\Timer\Timer;

class WebSocketService
{
    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;
    private $connection;

    /**
     * WebSocketService constructor.
     * @param String $socketServerPath
     * @param array $subProtocols
     * @param array $headers
     */
    public function __construct(?String $socketServerPath = null, array $subProtocols = [], $headers = [])
    {
        $socketServerPath = $socketServerPath ?? config('services.sockets.url');
        $this->loop = ReactFactory::create();
        $connector = new Connector($this->loop);
        $this->connection = call_user_func($connector, $socketServerPath, $subProtocols, $headers);
        $runHasBeenCalled = false;

        $this->loop->addTimer(Timer::MIN_INTERVAL, function () use (&$runHasBeenCalled) {
            $runHasBeenCalled = true;
        });

        register_shutdown_function(function() use (&$runHasBeenCalled) {
            if (!$runHasBeenCalled) {
                $this->loop->run();
            }
        });
    }

    /**
     * @param array $arr
     * @return mixed
     */
    public function send(Array $arr)
    {
        return $this->connection->then(function($conn) use ($arr) {
            $conn->send(collect($arr)->toJson());
        }, function ($e) {
            echo "Could not connect: {$e->getMessage()}\n";
        });
    }
}
