<?php

namespace gib\queue\rabbit;

class Producer extends Channel
{
    protected $exchange;

    public function __construct($connection, $exchange_name)
    {
        parent::__construct($connection);
        $this->exchange = new \AMQPExchange($this->channel);
        $this->exchange->setName($exchange_name);
    }

    public function __destruct()
    {
        $this->exchange = null;
    }

    public function push($msg, $routing_key = null, $attrs = [])
    {
        $attrs = array_merge(["delivery_mode" => 2], $attrs);
        if (empty($msg)) {
            throw new \AMQPException("Push empty message to queue is depricated!");
        }
        if (false === $this->exchange->publish($msg, $routing_key, AMQP_NOPARAM, $attrs)) {
            throw new \AMQPException("Error when pushing into queue!");
        }
    }

    public function handle()
    {
        return $this->exchange;
    }
}
