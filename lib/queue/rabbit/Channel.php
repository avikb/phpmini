<?php

namespace gib\queue\rabbit;

class Channel implements \gib\util\Transactional
{
    protected $connection;
    protected $channel;

    public function __construct($connection, $prefetchCount = null)
    {
        $this->connection = $connection;
        $this->channel = new \AMQPChannel($connection->handle());
        if (!empty($prefetchCount)) {
            $this->channel->setPrefetchCount(intval($prefetchCount));
        }
    }

    public function __destruct()
    {
        $this->channel = null;
    }

    public function startTransaction()
    {
        return $this->channel->startTransaction();
    }

    public function commit()
    {
        $this->channel->commitTransaction();
    }

    public function rollback()
    {
        $this->channel->rollbackTransaction();
    }

    public function inTransaction()
    {
        return false;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function handle()
    {
        return $this->channel;
    }
}
