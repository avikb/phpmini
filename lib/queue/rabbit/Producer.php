<?php

namespace gib\queue\rabbit;


class Producer extends Channel
{
    private $exchange;
    private $routing_key;
    private $delivery_mode;

    public function __construct($connection, $exchange_name, $routing_key = null, $opts = array())
    {
        parent::__construct($connection);
        $opts = array_merge(array("delivery_mode" => 2), $opts);
        $this->exchange = new \AMQPExchange($this->channel);
        $this->exchange->setName($exchange_name);
        $this->routing_key = $routing_key;
        $this->delivery_mode = $opts["delivery_mode"];
    }

    public function __destruct()
    {
        $this->exchange = null;
    }

    public function push($msg)
    {
        if (empty($msg)) {
            throw new \AMQPException("Попытка вставить пустое вообщение в очередь!");
        }
        if (false === $this->exchange->publish($msg, $this->routing_key, AMQP_NOPARAM, array("delivery_mode" => $this->delivery_mode))) {
            throw new \AMQPException("Ошбика при вставке в очередь!");
        }
    }
}
