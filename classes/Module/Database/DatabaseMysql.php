<?php

declare(strict_types=1);

namespace classroom;

include_once('Database.php');

/**
 * Allow get and save data from/in mysql database.
 */
class DatabaseMysql extends Database {
    
    /**
     * @var string $_prefix Tables' prefix.
     */
    private $_prefix = '';
    
    /**
     * @var \mysqli $_mysqli mysqli object.
     */
    private $_mysqli = null;
    
    /**
     * Last executed query.
     */
    private $_lastQuery = null;
    /**
     * Class constructor.
     */
    public function __construct() {
        $config = Config::instance();
        $server = $config->get('database', 'server');
        $login = $config->get('database', 'login');
        $password = $config->get('database', 'password');
        $dbname = $config->get('database', 'name');
        $this->_prefix = $config->get('database', 'prefix');
        
        $this->_mysqli = new \mysqli($server, $login, $password, $dbname);
        if ($this->_mysqli->connect_errno) {
            die('Can\'t connect');
        }
        
    }
    
    /**
     * Gets data by id.
     */
    public function getById(string $table, string $id): ?array {
        $result = $this->getByKey($table, 'id', $id);
        
        return $result;
    }
    
    /**
     * Gets data by id.
     */
    public function getList(
        string $table, array $conditionsList,
        ?int $limit, ?int $offset
    ): ?array {
        $result = null;
        
        $conditionQuery = '';
        if (count($conditionsList)) {
            $conditionQueryList = array();
            foreach ($conditionsList as $key => $value) {
                $conditionQueryList[] = '`' . $this->_mysqli->escape_string((string) $key) . '` = "'
                    . $this->_mysqli->escape_string((string) $value) . '"';
            }
            
            $conditionQuery = ' where (' . implode(' and ', $conditionQueryList) . ')';
        }
        
        $limitQuery = '';
        if ($limit) {
            if ($offset) {
                $limitQuery = ' limit ' . (string) $offset . ', ' . (string) $limit;
            } else {
                 $limitQuery = ' limit ' . (string) $limit;
            }
        }
        
        $query = 'select * from `' . $this->_mysqli->escape_string($this->_prefix . $table) . '`'
            . $conditionQuery
            . $limitQuery;
        $this->_lastQuery = $query;
        
        $res = $this->_mysqli->query($query);
        if ($res) {
            $result = array();
            while ($row = $res->fetch_assoc()) {
                $result[] = $row;
            }
        }
        
        return $result;
    }
    
    /**
     * Gets count of records.
     */
    public function getCount(string $table, array $conditionsList): int {
        $count = 0;
        
        $conditionQuery = '';
        if (count($conditionsList)) {
            $conditionQueryList = array();
            foreach ($conditionsList as $key => $value) {
                $conditionQueryList[] = '`' . $this->_mysqli->escape_string((string) $key) . '` = "'
                    . $this->_mysqli->escape_string((string) $value) . '"';
            }
            
            $conditionQuery = ' where (' . implode(' and ', $conditionQueryList) . ')';
        }
        
        $query = 'select count(*) as count from `' . $this->_mysqli->escape_string($this->_prefix . $table) . '`'
            . $conditionQuery;
        $this->_lastQuery = $query;
        
        $res = $this->_mysqli->query($query);
        if ($res) {
            $row = $res->fetch_assoc();
            if (array_key_exists('count', $row)) {
                $count = (int) $row['count'];
            }
        }
        
        return $count;
    }
    
    /**
     * Gets data by key.
     */
    public function getByKey(string $table, string $key, string $value): ?array {
        $result = null;
        
        $query = 'select * from `' . $this->_mysqli->escape_string($this->_prefix . $table) . '`' . 
            ' where `' . $this->_mysqli->escape_string((string) $key) . '` = "' . 
            $this->_mysqli->escape_string((string) $value) . '"';
        $this->_lastQuery = $query;
        
        $res = $this->_mysqli->query($query);
        if ($res) {
            $row = $res->fetch_assoc();
            if ($row) {
                $result = $row;
            }
        }
        
        return $result;
    }
    
    /**
     * Saves the record in the database.
     */
    public function addRecord(string $table, array $data): ?string {
        $result = null;
        
        if (is_array($data) && count($data)) {
            $query = 'insert into ' . $this->_mysqli->escape_string($this->_prefix . $table);
            $keysArray = array();
            $valsArray = array();
            foreach ($data as $key => $val) {
                $keysArray[] = '`' . $this->_mysqli->escape_string((string) $key) . '`';
                $valsArray[] = '"' . $this->_mysqli->escape_string((string) $val) . '"';
            }
            
            $query .= ' (' . implode(', ', $keysArray) . ') ' . 
                ' values (' . implode(', ', $valsArray) . ')';
            $this->_lastQuery = $query;
            
            if ($this->_mysqli->query($query)) {
                $result = (string) $this->_mysqli->insert_id;
            }
        }
        
        return $result;
    }
    
    /**
     * Updates the record in the database.
     */
    public function updateRecord(string $table, array $data, string $primaryKey = 'id'): ?string {
        $result = null;
        
        if (is_array($data) && count($data) && array_key_exists($primaryKey, $data) && $data[$primaryKey]) {
            $query = 'update `' . $this->_mysqli->escape_string($this->_prefix . $table) . '` ';
            $valsArray = array();
            foreach ($data as $key => $val) {
                if ($key !== $primaryKey) {
                    $valsArray[] = '`' . $this->_mysqli->escape_string((string) $key) . '`'
                        . ' = '
                        . '"' . $this->_mysqli->escape_string((string) $val) . '"';
                }
            }
            
            if (count($valsArray)) {
                $query .= ' set ' . implode(', ', $valsArray)
                    . ' where `' . $this->_mysqli->escape_string((string) $primaryKey)
                    . '` = "' . $data[$primaryKey] . '"';
                $this->_lastQuery = $query;
                
                if ($this->_mysqli->query($query)) {
                    $result = (string) $data[$primaryKey];
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Gets last error information.
     */
    public function getLastError(): ?array {
        $error = null;
        
        if ($this->_mysqli->errno !== 0) {
            $error = array();
            $error['error'] = $this->_mysqli->error;
            $error['code'] = $this->_mysqli->errno;
            if ($this->_lastQuery) {
                $error['query'] = $this->_lastQuery;
            }
        }
        
        return $error;
    }
    
}
