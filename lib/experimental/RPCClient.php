<?php

namespace gib\experimental;

use \gib\queue\rabbit\Producer;
use \gib\queue\rabbit\Consumer;

class RPCClient extends Producer
{
    protected $consumer;
    protected $replyTo;

    public function __construct($connection, $exchange_name)
    {
        parent::__construct($connection, $exchange_name);
        $this->consumer = new Consumer($connection);
        $this->replyTo = $this->consumer->getQueueName();
    }

    public function call($msg, $route_key = null, int $timeout = 0)
    {
        $this->push($msg, $route_key, ['reply_to' => $this->replyTo]);
        // TODO:
        // use consume() mutch cool, but...
        // consume multiplies consumers on queue...
        $sum = 0;
        $sleep = 1000;
        while (true) {
            $msg = $this->consumer->pop(true);
            if ($msg) {
                return $msg->getBody();
            }
            if ($sleep < 500000) {
                $sleep *= 2;
            }
            $sum += $sleep;
            if ($timeout && $sum / 1000 > $timeout) {
                throw new \Exception('timeout');
            }
            usleep($sleep);
        }
    }
}
