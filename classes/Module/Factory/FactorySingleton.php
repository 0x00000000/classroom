<?php

declare(strict_types=1);

namespace classroom;

include_once('FactoryAbstract.php');

/**
 * Creates modules and models.
 * Implement singleton pattern functionality.
 */
abstract class FactorySingleton extends FactoryAbstract {
    
    /**
     * @var string $_baseType Name prefix of singleton class.
     */
    protected static $_baseType = 'Factory';
    
    /**
     * @var string|null $_type Name postrix of singleton class.
     */
    protected static $_type = null;
    
    /**
     * @var Config $_instance Singleton object.
     */
    protected static $_instance = null;
    
    /**
     * Gets singleton object's module name.
     */
    protected static function getModuleName(): ?string {
        $moduleName = null;
        
        if (self::$_baseType && self::$_type) {
            $moduleName = self::$_baseType . self::$_type;
        }
        
        return $moduleName;
    }
    
    /**
     * Gets base module name for singleton object.
     */
    protected static function getBaseModuleName(): string {
        return self::$_baseType;
    }
    
    /**
     * Sets module name postrix for singleton object.
     */
    public static function setType(string $type): bool {
        $result = false;
        
        if (! self::$_type) {
            if ($type) {
                $result = true;
                self::$_type = $type;
            }
        }
        
        return $result;
    }
    
    /**
     * Gets singleton object.
     */
    public static function instance(): Factory {
        if (! self::$_instance && self::$_type) {
            $moduleName = self::getModuleName();
            $baseModuleName = self::getBaseModuleName();
            $loaded = Core::loadClass($moduleName, 'Module/' . $baseModuleName);
            if ($loaded) {
                $className = Core::getNamespace() . $moduleName;
                self::$_instance = new $className();
            }
        }
        
        return self::$_instance;
    }
    
}