<?php
namespace gib\db;

class Mysql implements \gib\util\Transactional
{
  
    private $dbh = null;
  
    private $dsn;
    private $dblogin;
    private $dbpassword;
    private $storeQueryCallback = null;
  
    public function __construct($dsn, $lgn, $pass, $storeQueryCallback)
    {
        $this->dsn = $dsn;
        $this->dblogin = $lgn;
        $this->dbpassword = $pass;
        $this->storeQueryCallback = $storeQueryCallback;
    }

    public function __destruct()
    {
        $this->dbh = null;
    }
   
    public function checkConnect()
    {
        if ($this->dbh == null) {
            $this->dbh = new \PDO($this->dsn, $this->dblogin, $this->dbpassword);
            $this->dbh->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->query("set names utf8");
        }
    }

    public function setAttribute($key, $val)
    {
        $this->checkConnect();
        return $this->dbh->setAttribute($key, $val);
    }
  
    public function startTransaction()
    {
        $this->checkConnect();
        return $this->dbh->beginTransaction();
    }
  
    public function inTransaction()
    {
        $this->checkConnect();
        return $this->dbh->inTransaction();
    }
  
    public function rollback()
    {
        if (!empty($this->dbh)) {
            return $this->dbh->rollback();
        }
    }
  
    public function commit()
    {
        return $this->dbh->commit();
    }

    public function query($sql, $vals = array(), $buffered = true)
    {
        $this->checkConnect();
        if ($this->storeQueryCallback) {
            $this->storeQueryCallback($sql);
        }
        $stat = $this->dbh->prepare($sql);
        if (count($vals)) {
            foreach ($vals as $k => $v) {
                $stat->bindValue($k, $v);
            }
        }
        $this->dbh->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $buffered);
        $stat->execute();
        return $stat;
    }
  
    public function queryArray($sql, $vals = array())
    {
        $stat = $this->query($sql, $vals);
        $res = $stat->fetchAll(\PDO::FETCH_OBJ);
        return $res;
    }
  
    public function queryArrayUnbuffered($sql, $vals = array())
    {
        $stat = $this->query($sql, $vals, false);
        while (($row = $stat->fetchObject()) !== false) {
            yield $row;
        }
    }

    public function queryObj($sql, $vals = array())
    {
        $arr = $this->queryArray($sql, $vals);
        if (count($arr) != 1) {
            throw new \Exception("Database::queryObj() bad result (".count($arr).")");
        }
        return $arr[0];
    }
  
    public function insertRow($sql, $vals = array())
    {
        $this->query($sql, $vals, true, true);
        return $this->dbh->lastInsertId();
    }
  
    public function queryTotal()
    {
        $rows = $this->query("select FOUND_ROWS() as cnt")->fetchAll();
        return intval($rows[0]['cnt']);
    }
  
    public function prepare($sql)
    {
        $this->checkConnect();
        return $this->dbh->prepare($sql);
    }
  
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }
}
