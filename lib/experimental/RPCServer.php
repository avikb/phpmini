<?php

namespace gib\experimental;

use \gib\queue\rabbit\Producer;
use \gib\queue\rabbit\Consumer;

class RPCServer extends Consumer
{
    protected $producer;

    public function __construct($connection, $queue_name, $back_exchange = '', $prefetchCount = 1)
    {
        parent::__construct($connection, $queue_name, $prefetchCount);
        $this->producer = new Producer($connection, $back_exchange);
    }

    public function serve($callback, bool $full = false)
    {
        $this->callback = $callback;
        $func = function ($env) use ($full) {
            $answer = call_user_func($this->callback, $full ? $env : $env->getBody());
            if (!empty($env->getReplyTo()) || !empty($answer)) {
                $this->producer->push($answer, $env->getReplyTo(), ['delivery_mode' => 1, 'expiration' => 100000]);
            }
            $this->ack($env);
        };
        $this->queue->consume($func);
    }
}
