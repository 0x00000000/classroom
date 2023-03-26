<?php

declare(strict_types=1);

namespace Classroom\System;

/**
 * Functionality for work with file system.
 */
class FileSystem {
    private const MOCK_FILE_SIZE = 1024;

    /**
     * @var string|null $_root File system path to script's root directory.
     */
    private static $_root = null;

    /**
     * @var bool $_isTestMode Is application runs in test mode.
     */
    private static $_isTestMode = false;

    /**
     * Sets test mode.
     *
     * @param bool $isTestMode Is application was launched in test mode.
     * @return bool Is test mode was successfully set.
     */
    public static function setTestMode(bool $isTestMode = false): bool {
        self::$_isTestMode = $isTestMode;

        return true;
    }

    /**
     * Gets file system path to script's root directory.
     * 
     * @return string Script's root directory.
     */
    public static function getRoot(): string {
        if (! self::$_root) {
            self::$_root = dirname(dirname(dirname(__FILE__)));
        }
        
        return self::$_root;
    }
    
    /**
     * Gets file system path to writable directory for data.
     * 
     * @return string Data directory or null if it is absent.
     */
    public static function getDataDir(): ?string {
        $dir = self::getRoot() . self::getDS() . 'data';
        if (! is_dir($dir)) {
            $dir = null;
        }
        
        return $dir;
    }
    
    /**
     * Gets directory separator.
     * 
     * @return string Directory separator.
     */
    public static function getDirectorySeparator(): string {
        return DIRECTORY_SEPARATOR;
    }
    
    /**
     * Gets directory separator.
     * 
     * @return string Directory separator.
     */
    public static function getDS(): string {
        return DIRECTORY_SEPARATOR;
    }
    
    /**
     * Gets php scripts extension used in this project.
     * 
     * @return string Scripts extension.
     */
    public static function getScriptExtension(): string {
        return '.php';
    }

    /**
     * Moved uploaded file if test mode is false.
     *
     * @param string $fromPath
     * @param string $toPath
     *
     * @return bool
     */
    public static function moveUploadedFile(string $fromPath, string $toPath): bool {
        if (! self::$_isTestMode) {
            return move_uploaded_file($fromPath, $toPath);
        } else {
            $pathParts = pathinfo($toPath);
            $result = is_file($fromPath)
                && is_dir($pathParts['dirname'])
                && strlen($pathParts['basename']);
            return $result;
        }
    }

    /**
     * Get file size if test mode is false.
     *
     * @param string $path
     *
     * @return false|int
     */
    public static function getFileSize(string $path)  {
        if (! self::$_isTestMode) {
            return filesize($path);
        } else {
            if (file_exists($path)) {
                return filesize($path);
            } else {
                return self::MOCK_FILE_SIZE;
            }
        }
    }
}