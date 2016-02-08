<?php

namespace gib\queue\rabbit;


class Connection
{
    private $connection;

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
        // hard code for config
        if (isset($credentials["user"])) {
            $credentials["login"] = $credentials["user"];
        }
        // ---
        $this->connection = new \AMQPConnection($credentials);
        $this->connection->connect();
    }

    public function __destruct()
    {
        $this->connection->disconnect();
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
