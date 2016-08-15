<?php

namespace gib\queue\rabbit;


class Consumer extends Channel
{
    private $queue;

    public function __construct($connection, $queue_name, $params = [])
    {
        parent::__construct($connection, $params);
        $this->queue = new \AMQPQueue($this->channel);
        $this->queue->setName($queue_name);
    }

    public function __destruct()
    {
        $this->queue = null;
    }

    public function pop()
    {
        return $this->queue->get();
    }

    public function consume($callback)
    {
        $this->callback = $callback;
        $func = function ($env) {
            call_user_func($this->callback, $env->getBody());
            $this->ack($env);
        };
        $this->queue->consume($func);
    }

    public function ack(&$env)
    {
        $this->queue->ack($env->getDeliveryTag());
    }

    public function nack(&$env)
    {
        $this->queue->nack($env->getDeliveryTag());
    }
}
