<?php

declare(strict_types=1);

namespace classroom;

include_once('Model.php');

/**
 * Abstract model class that stores data in database.
 */
abstract class ModelDatabase extends Model {
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = null;
    
    /**
     * @var array $_hiddenPropsList List of protected properties that are not accessible from outside.
     */
    protected $_hiddenPropsList = array('_table', '_hiddenPropsList', '_database');
    
    /**
     * Database object.
     */
    protected $_database = null;
    
    /**
     * Class constructor.
     */
    public function __construct() {
    }
    
    /**
     * Load object's data from database by id.
     */
    public function loadById(string $id): bool {
        $result = false;
        
        if ($this->getDatabase()) {
            if ($this->_table && $id) {
                $dbData = $this->getDatabase()->getById($this->_table, $id);
                if ($dbData) {
                    $result = $this->setDataFromDB($dbData);
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Loads object's data from database by key.
     */
    public function loadByKey(string $key, string $value): bool {
        $result = false;
        
        if ($this->getDatabase()) {
            if ($this->_table && $key) {
                $dbData = $this->getDatabase()->getByKey($this->_table, $key, $value);
                if ($dbData) {
                    $result = $this->setDataFromDB($dbData);
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Gets data by id.
     */
    public function getList(array $conditionsList, ?int $limit, ?int $offset): ?array {
        $result = false;
        
        if ($this->getDatabase()) {
            if ($this->_table) {
                $result = $this->getDatabase()->getList(
                    $this->_table,
                    $conditionsList,
                    $limit,
                    $offset
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Gets count of records.
     */
    public function getCount(array $conditionsList): int {
        $result = false;
        
        if ($this->getDatabase()) {
            if ($this->_table) {
                $result = $this->getDatabase()->getCount(
                    $this->_table,
                    $conditionsList
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Saves object's data to database.
     */
    public function save(): ?string {
        $result = null;
        
        if ($this->getDatabase()) {
            if ($this->_table) {
                $data = $this->getDataForDB();
                
                if (count($data)) {
                    if (! $this->id) {
                        $id = $this->getDatabase()->addRecord($this->_table, $data);
                        if ($id) {
                            $this->id = $id;
                            $result = $this->id;
                        } else {
                        }
                    } else {
                        $this->getDatabase()->updateRecord($this->_table, $data);
                        $result = $this->id;
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Gets object's data as associative array.
     */
    public function getDataAssoc(): array {
        $data = array();
        
        foreach ($this as $propName => $value) {
            if ($this->isDataProp($propName)) {
                if (! is_null($value)) {
                    $key = substr($propName, 1);
                    $data[$key] = $value;
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Sets database object.
     */
    public function setDatabase(Database $database): bool {
        $result = false;
        if (is_object($database) && $database instanceof Database) {
            $this->_database = $database;
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Gets last error information.
     */
    public function getLastError(): ?array {
        $result = null;
        
        if ($this->getDatabase()) {
            $result = $this->getDatabase()->getLastError();
        }
        
        return $result;
    }
    
    /**
     * Sets database object.
     */
    protected function getDatabase() {
        return $this->_database;
    }
    
    /**
     * Gets data for current object. This data may be used for writing to database.
     */
    private function getDataForDB(): array {
        $data = $this->getDataAssoc();
        $dbData = array();
        
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $newKey = preg_replace_callback(
                    '/[A-Z]/',
                    function($matches) {
                        return '_' . strtolower($matches[0]);
                    },
                    $key
                );
                $dbData[$newKey] = $val;
            }
        }
        
        return $dbData;
    }
    
    /**
     * Sets data from database to current object.
     */
    private function setDataFromDB(array $dbData): bool {
        $result = false;
        $data = array();
        if (is_array($dbData)) {
            foreach ($dbData as $key => $value) {
                $propName = preg_replace_callback(
                    '/_\w/',
                    function($matches) {
                        return substr(strtoupper($matches[0]), 1);
                    },
                    $key
                );
                $propName = '_' . $propName;
                
                if ($this->isDataProp($propName)) {
                    $data[$propName] = $value;
                }
            }
            
            if (count($data)) {
                $result = true;
                foreach ($this as $propName => $value) {
                    if ($this->isDataProp($propName)) {
                        if (array_key_exists($propName, $data)) {
                            $this->$propName = $data[$propName];
                        } else {
                            $this->$propName = null;
                        }
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Some properties may be set by this magic meghod.
     * Properties, start from "_", has only one "_" in it's name and
     * not in _hiddenPropsList list.
     */
    public function __set(string $name, $value): void {
        $propertyName = '_' . $name;
        if ($this->isDataProp($propertyName)) {
            $methodName = 'set' . ucfirst($name);
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            } else {
                $this->$propertyName = $value;
            }
        }
    }
    
    /**
     * Some properties may be get by this magic meghod.
     * Properties, start from "_", has only one "_" in their names and
     * are not in _hiddenPropsList list.
     */
    public function __get(string $name) {
        $result = null;
        $propertyName = '_' . $name;
        
        if ($this->isDataProp($propertyName)) {
            $methodName = 'get' . ucfirst($name);
            if (method_exists($this, $methodName)) {
                $result = $this->$methodName();
            } else {
                $result = $this->$propertyName;
            }
        }
        
        return $result;
    }
    
    /**
     * Checks if property is can be used from outside.
     * Properties, start from "_", has only one "_" in their names and
     * are not in _hiddenPropsList list.
     */
    protected function isDataProp($propName) {
        $result = false;
        
        if (! in_array($propName, $this->_hiddenPropsList)) {
            if (substr($propName, 0, 1) === '_') {
                if (strpos($propName, '_', 1) === false) {
                    $result = true;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Adds property to _hiddenPropsList list.
     */
    protected function addHiddenProperty($propertyName) {
        $result = false;
        if (is_string($propertyName) && strlen($propertyName) && is_array($this->_hiddenPropsList)) {
            if (! in_array($propertyName, $this->_hiddenPropsList)) {
                $this->_hiddenPropsList[] = $propertyName;
            }
            $result = true;
        }
        
        return $result;
    }
    
}
