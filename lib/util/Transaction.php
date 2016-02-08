<?php

namespace gib\util;


class Transaction
{
    private $obj = null;

    public function __construct($obj)
    {
        if ($obj instanceof Transactional) {
            if (!$obj->inTransaction()) {
                $this->obj = $obj;
                if (!$this->obj->startTransaction()) {
                    throw new \Exception("startTransaction(".get_class($obj).") fail");
                }
            }
        } else {
            throw new \Exception("get_class(".print_r($obj, true).")) is not a Transactional object");
        }
    }

    public function __destruct()
    {
        if (!is_null($this->obj)) {
            $this->obj->rollback();
        }
    }

    public function commit()
    {
        if (!is_null($this->obj)) {
            $this->obj->commit();
        }
        $this->obj = null;
    }

    public function rollback()
    {
        if (!is_null($this->obj)) {
            $this->obj->rollback();
        }
        $this->obj = null;
    }
}
