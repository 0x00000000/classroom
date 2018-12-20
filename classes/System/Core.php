<?php

declare(strict_types=1);

namespace classroom;

include_once('FileSystem.php');

/**
 * Common functionality.
 */
class Core {
    
    /**
     * @var string|null $_applicationType Application type.
     */
    private static $_applicationType = null;
    
    /**
     * @var bool $_isTestMode Is application runs in test mode.
     */
    private static $_isTestMode = false;
    
    /**
     * @var string $_namespace Script's namespace.
     */
    private static $_namespace = 'classroom\\';
    
    /**
     * Sets application type.
     * This type will be used by Facroty for creating modules.
     * 
     * @param string $applicationType Application type.
     * @param bool $isTestMode Is application was launched in test mode. Used in unit tests.
     * @return bool Is application type was successfully set.
     */
    public static function setApplicationType(string $applicationType, bool $isTestMode = false): bool {
        $result = false;
        
        if (! self::$_applicationType) {
            if ($applicationType) {
                if ($isTestMode) {
                    self::$_isTestMode = true;
                }
                self::$_applicationType = $applicationType;
                $result = self::init();
            }
        }
        
        return $result;
    }
    
    /**
     * Gets application type.
     * 
     * @return string|null Application type.
     */
    public static function getApplicationType(): ?string {
        return self::$_applicationType;
    }
    
    /**
     * Gets namespace.
     * 
     * @return string Current namespace.
     */
    public static function getNamespace(): string {
        return self::$_namespace;
    }
    
    /**
     * Loads class.
     * 
     * @param string $className Class name.
     * @param string|null $path Path to class file.
     * @return bool Is class was succsessfully loaded.
     */
    public static function loadClass(string $className, string $path = null): bool {
        $result = false;
        
        if (strlen($className)) {
            $root = FileSystem::getRoot();
            $ds = FileSystem::getDS();
            $extension = FileSystem::getScriptExtension();
            
            $extendedPath = $root . $ds . 'classes' . $ds . str_replace('/', $ds, $path);
            
            $fullPath = $extendedPath . $ds . $className . $extension;
            if (is_file($fullPath)) {
                include_once($fullPath);
                $result = true;
            }
        }
        
        return $result;
    }
    
    /**
     * Writes error message and ends script's execution.
     * 
     * @param string $message Error message to be shown.
     */
    public static function FatalError(string $message = null): void {
        $errorMessage = 'Fatal error.';
        if ($message) {
            $errorMessage .= ' ' . $message;
        }
        
        die($errorMessage);
    }
    
    /**
     * Creates necessary modules for script's work.
     */
    private static function init(): bool {
        $result = false;
        
        if (self::getApplicationType()) {
            $result = true;
            
            self::loadClass('Factory', 'Module/Factory');
            Factory::setType(self::getApplicationType());
            if (self::$_isTestMode) {
                Factory::instance()->setTestMode();
            }
            
            Factory::instance()->createRegistry();
            
            Factory::instance()->createConfig();
            
            $database = Factory::instance()->createDatabase();
            Factory::instance()->setDatabase($database);
            
        }
        
        return $result;
    }
    
}