<?php

namespace gib\queue\rabbit;


class Channel implements \gib\util\Transactional
{
    protected $connection;
    protected $channel;

    public function __construct($connection, $params = [])
    {
        $this->connection = $connection;
        $this->channel = new \AMQPChannel($connection->getConnection());
        if (isset($params['prefetch_count'])) {
            $this->channel->setPrefetchCount(intval($params['prefetch_count']));
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
}
