<?php

namespace gib\queue\rabbit;

class RPCServer extends Consumer
{
    protected $producer;

    public function __construct($connection, $queue_name, $back_exchange = '')
    {
        parent::__construct($connection, $queue_name);
        $this->producer = new Producer($connection, $back_exchange);
    }

    public function serve($callback, bool $full = false)
    {
        $this->callback = $callback;
        $func = function ($env) use ($full) {
            $answer = call_user_func($this->callback, $full ? $env : $env->getBody());
            if (!empty($env->getReplyTo()) || !empty($answer)) {
                $this->producer->push($answer, $env->getReplyTo(), ['delivery_mode' => 1, 'expiration' => 10000]);
            }
            $this->ack($env);
        };
        $this->queue->consume($func);
    }
}
