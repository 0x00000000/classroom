<?php

declare(strict_types=1);

namespace classroom;

include_once('Config.php');

/**
 * Stores configuration data for other modules.
 */
class ConfigBase extends Config {
    /**
     * @var array $_data Stores configuration data.
     */
    protected $_data = array();
    
    /**
     * Class constructor.
     */
    public function __construct() {
    }
    
    /**
     * Gets data from configuration.
     */
    public function get(string $section, string $name = null) {
        $result = null;
        
        if (
            array_key_exists($section, $this->_data)
            && is_array($this->_data[$section])
        ) {
            if ($name && array_key_exists($name, $this->_data[$section])) {
                $result = $this->_data[$section][$name];
            } else {
                $result = $this->_data[$section];
            }
        }
        
        return $result;
    }
    
    /**
     * Adds data from configuration.
     * Existed data con't be rewrited.
     */
    public function add(string $section, string $name, $value): bool {
        $result = false;

        if ($section && $name) {
            if (
                ! array_key_exists($section, $this->_data)
                || ! is_array($this->_data[$section])
            ) {
                $this->_data[$section] = array();
            }
            
            $this->_data[$section][$name] = $value;
            
            $result = true;
        }
        
        return $result;
    }
    
}
