<?php

namespace gib\queue\rabbit;

class Consumer extends Channel
{
    protected $queue;

    public function __construct($connection, $queue_name = null, $prefetchCount = null, $args = null)
    {
        parent::__construct($connection, $prefetchCount);
        $this->queue = new \AMQPQueue($this->channel);
        if (!empty($queue_name)) {
            $this->queue->setName($queue_name);
        } else {
            // declare new queue
            $this->queue->setFlags(AMQP_EXCLUSIVE|AMQP_AUTODELETE);
            $this->queue->declare();
        }
        if (is_array($args)) {
            $this->queue->setArguments($args);
        }
    }

    public function __destruct()
    {
        $this->queue = null;
    }

    public function pop($autoAck = false)
    {
        return $this->queue->get($autoAck ? AMQP_AUTOACK : 0);
    }

    public function consume($callback, bool $asObject = false)
    {
        return $this->queue->consume(function ($env) use ($callback, $asObject) {
            $ret = call_user_func($callback, $asObject ? $env : $env->getBody());
            $this->ack($env);
            return $ret;
        });
    }

    public function ack($env)
    {
        $this->queue->ack($env->getDeliveryTag());
    }

    public function nack($env)
    {
        $this->queue->nack($env->getDeliveryTag());
    }

    public function getQueueName()
    {
        return $this->queue->getName();
    }

    public function handle()
    {
        return $this->queue;
    }
}
