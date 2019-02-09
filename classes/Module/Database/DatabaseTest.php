<?php

declare(strict_types=1);

namespace classroom;

include_once('Database.php');

/**
 * Allow get and save data from/in in ram. Used for unit tests.
 */
class DatabaseTest extends Database {
    
    /**
     * @var array $_data Stores data.
     */
    protected $_data = array();
    
    /**
     * Last added record's id.
     */
    protected $_lastId = 0;
    
    /**
     * Class constructor.
     */
    public function __construct() {
        
    }
    
    /**
     * Gets data by id.
     */
    public function getById(string $table, string $pk, string $primaryKey = 'id'): ?array {
        $result = null;
        
        if (array_key_exists($table, $this->_data)) {
            if (array_key_exists($pk, $this->_data[$table])) {
                $result = $this->_data[$table][$pk];
            }
        }
        
        return $result;
    }
    
    /**
     * Gets data by id.
     */
    public function getList(
        string $table, array $conditionsList,
        ?int $limit, ?int $offset
    ): array {
        $result = array();
        
        if (array_key_exists($table, $this->_data)) {
            $counter = 0;
            foreach ($this->_data[$table] as $pk => $record) {
                $fit = true;
                foreach ($conditionsList as $key => $value) {
                    if (! array_key_exists($key, $record) || $record[$key] !== $value) {
                        $fit = false;
                        break;
                    }
                }
                
                if ($fit) {
                    $result[] = $record;
                    $counter++;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Gets count of records.
     */
    public function getCount(string $table, array $conditionsList): int {
        $count = 0;
        
        if (array_key_exists($table, $this->_data)) {
            foreach ($this->_data[$table] as $pk => $record) {
                $fit = true;
                foreach ($conditionsList as $key => $value) {
                    if (! array_key_exists($key, $record) || $record[$key] !== $value) {
                        $fit = false;
                        break;
                    }
                }
                
                if ($fit) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    /**
     * Saves the record in the database.
     */
    public function addRecord(string $table, array $data): ?string {
        $result = null;
        
        if (is_array($data) && count($data)) {
            if (! array_key_exists($table, $this->_data)) {
                $this->_data[$table] = array();
            }
            
            $this->_lastId++;
            $stringId = (string) $this->_lastId;
            $data['id'] = $stringId;
            $this->_data[$table][$stringId] = $data;
            $result = $stringId;
        }
        
        return $result;
    }
    
    /**
     * Updates the record in the database.
     */
    public function updateRecord(string $table, array $data, string $primaryKey = 'id'): ?string {
        $result = null;
        
        if (is_array($data) && count($data) && array_key_exists($primaryKey, $data) && $data[$primaryKey]) {
            // For this implementation DB we may ignore $primaryKey name and use it's value.
            $record = $this->getById($table, $data[$primaryKey]);
            if ($record) {
                foreach ($data as $key => $val) {
                    if ($key !== $primaryKey) {
                        $record[$key] = $val;
                    }
                }
                
                $this->_data[$table][$data[$primaryKey]] = $record;
                $result = (string) $data[$primaryKey];
            }
        }
        
        return $result;
    }
    
    /**
     * Deletes the record in the database.
     */
    public function deleteRecord(string $table, string $pk, string $primaryKey = 'id'): ?string {
        $result = false;
        
        if (array_key_exists($table, $this->_data)) {
            if (array_key_exists($pk, $this->_data['$table'])) {
                unset($this->_data['$table'][$pk]);
                $result = true;
            }
        }
        
        return $result;
    }
    
    /**
     * Gets last error information.
     */
    public function getLastError(): ?array {
        $error = null;
        
        return $error;
    }
    
    /**
     * Escapes string for safe using in sql statements.
     */
    protected function escape(string $value): string {
        $result = $value;
        
        return $result;
    }
    
}
