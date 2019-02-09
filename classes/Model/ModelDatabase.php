<?php

declare(strict_types=1);

namespace classroom;

include_once('Model.php');

/**
 * Abstract model class that stores data in database.
 */
abstract class ModelDatabase extends Model {
    
    /**
     * Field types.
     */
    public const TYPE_TEXT = 'text';
    public const TYPE_BOOL = 'bool';
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = null;
    
    /**
     * @var array $_hiddenPropsList List of protected properties that are not accessible from outside.
     */
    protected $_hiddenPropsList = array('_table', '_hiddenPropsList', '_boolPropsList', '_database');
    
    /**
     * @var array $_boolPropsList List of boolean properties.
     */
    protected $_boolPropsList = array();
    
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
     * Gets data from DB.
     */
    protected function getDataList(array $conditionsList, ?int $limit, ?int $offset): array {
        $result = array();
        
        if ($this->getDatabase()) {
            if ($this->_table) {
                $dbDataList = $this->getDatabase()->getList(
                    $this->_table,
                    $conditionsList,
                    $limit,
                    $offset
                );
                if ($dbDataList) {
                    // $dbDataList can be null
                    $result = $dbDataList;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Gets data for single record from DB.
     */
    protected function getDataRecord(array $conditionsList): array {
        $result = array();
        
        $dbDataList =  $this->getDataList(
            $conditionsList,
            1,
            0
        );
        if ($dbDataList && count($dbDataList)) {
            $result = $dbDataList[0];
        }
        
        return $result;
    }
    
    /**
     * Gets array of models.
     */
    public function getModelsList(array $conditionsList, ?int $limit, ?int $offset): array {
        $result = array();
        
        $dbDataList = $this->getDataList(
            $conditionsList,
            $limit,
            $offset
        );
        $className = get_class($this);
        if ($dbDataList && count($dbDataList)) {
            foreach ($dbDataList as $dbData) {
                $model = new $className();
                $model->setDataFromDB($dbData);
                $result[] = $model;
            }
        }
        
        return $result;
    }
    
    /**
     * Gets one of model.
     */
    public function getOneModel(array $conditionsList): ?Model {
        $result = null;
        
        $dbData = $this->getDataRecord($conditionsList);
        if ($dbData && count($dbData)) {
            $className = get_class($this);
            $model = new $className();
            if ($model->setDataFromDB($dbData)) {
                $result = $model;
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
            if ($this->isDataProperty($propName)) {
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
    public function getDataForDB(): array {
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
    protected function setDataFromDB(array $dbData): bool {
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
                
                if ($this->isDataProperty($propName)) {
                    $data[$propName] = $value;
                }
            }
            
            if (count($data)) {
                $result = true;
                foreach ($this as $propName => $value) {
                    if ($this->isDataProperty($propName)) {
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
        if ($this->isDataProperty($propertyName)) {
            $methodName = 'set' . ucfirst($name);
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            } else {
                $valuePrepared = $this->preparePropertyBeforeSet($propertyName, $value);
                $this->$propertyName = $valuePrepared;
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
        
        if ($this->isDataProperty($propertyName)) {
            $methodName = 'get' . ucfirst($name);
            if (method_exists($this, $methodName)) {
                $result = $this->$methodName();
            } else {
                $value = $this->$propertyName;
                $valuePrepared = $this->preparePropertyBeforeGet($propertyName, $value);
                $result = $valuePrepared;
            }
        }
        
        return $result;
    }
    
    /**
     * Prepare property before setting to object porperty.
     */
    protected function preparePropertyBeforeSet($propertyName, $value) {
        if ($this->isBoolProperty($propertyName)) {
            if ($value) {
                $preparedValue = '1';
            } else {
                $preparedValue = '0';
            }
        } else {
            $preparedValue = $value;
        }
        
        return $preparedValue;
    }
    
    /**
     * Prepare property before getting from object porperty.
     */
    protected function preparePropertyBeforeGet($propertyName, $value) {
        if ($this->isBoolProperty($propertyName)) {
            $preparedValue = ($value === '1');
        } else {
            $preparedValue = $value;
        }
        
        return $preparedValue;
    }
    
    /**
     * Checks if property is can be used from outside.
     * Properties, start from "_", has only one "_" in their names and
     * are not in _hiddenPropsList list.
     * 
     * Use inner porperties names, f e _categoryName.
     */
    protected function isDataProperty($propertyName) {
        $result = false;
        
        if (! in_array($propertyName, $this->_hiddenPropsList)) {
            if (substr($propertyName, 0, 1) === '_') {
                if (strpos($propertyName, '_', 1) === false) {
                    $result = true;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Checks if property is boolean.
     * 
     * Use inner porperties names, f e _categoryName.
     */
    protected function isBoolProperty($propertyName) {
        return in_array($propertyName, $this->_boolPropsList);
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
    
    /**
     * Adds property to _boolPropsList list.
     */
    protected function addBoolProperty($propertyName) {
        $result = false;
        if (is_string($propertyName) && strlen($propertyName) && is_array($this->_boolPropsList)) {
            if (! in_array($propertyName, $this->_boolPropsList)) {
                $this->_boolPropsList[] = $propertyName;
            }
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Gets property type.
     */
    public function getPropertyType(string $propertyName): ?string {
        $type = null;
        
        if ($propertyName) {
            $innerPropertyName = '_' . $propertyName;
            if ($this->isBoolProperty($propertyName)) {
                $type = self::TYPE_BOOL;
            } else {
                $type = self::TYPE_TEXT;
            }
        }
        
        return $type;
    }
    
}
