<?php

declare(strict_types=1);

namespace Classroom\Module\Config;

use Classroom\System\FileSystem;

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
        
        if (! array_key_exists($section, $this->_data)) {
            $this->_data[$section] = $this->loadConfigFromFile($section);
        }
        
        if (
            array_key_exists($section, $this->_data)
            && is_array($this->_data[$section])
        ) {
            if (is_null($name)) {
                $result = $this->_data[$section];
            } else {
                if (array_key_exists($name, $this->_data[$section])) {
                    $result = $this->_data[$section][$name];
                } else {
                    $result = null;
                }
            }
        }
        
        return $result;
    }
    
    protected function getConfigDirectory(): string {
        return FileSystem::getRoot() . FileSystem::getDS() . 'config';
    }
    
    protected function loadConfigFromFile(string $section): ?array {
        $result = null;
        $path = $this->getConfigDirectory() . FileSystem::getDS() . $section . '.php';
        if (is_file($path)) {
            $result = @include($path);
        }
        
        return $result;
    }
    
}
