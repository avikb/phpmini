<?php

namespace gib\queue\rabbit;

class Connection
{
    protected $connection;

    public function __construct($credentials = array())
    {
        $credentials = array_merge(
            array(
                "login" => "guest",
                "password" => "guest",
                "host" => "localhost",
                "port" => "5672",
                "vhost" => "/"
            ),
            $credentials
        );
        $this->connection = new \AMQPConnection($credentials);
        $this->connection->connect();
    }

    public function __destruct()
    {
        $this->connection->disconnect();
    }

    public function handle()
    {
        return $this->connection;
    }
}
