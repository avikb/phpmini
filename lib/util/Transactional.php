<?php

namespace gib\util;


interface Transactional
{
    public function startTransaction();
    public function commit();
    public function rollback();
    public function inTransaction();
}
