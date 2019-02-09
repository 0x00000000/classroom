<?php

declare(strict_types=1);

namespace classroom;

/**
 * Abstract model class.
 */
abstract class ModelAbstract {
    
    /**
     * Loads object's data by id.
     */
    abstract public function loadById(string $id): bool;
    
    /**
     * Saves object's data.
     */
    abstract public function save(): ?string;
    
    /**
     * Gets object's data as associative array.
     */
    abstract public function getDataAssoc(): array;
    
    /**
     * Sets database object.
     */
    abstract public function setDatabase(Database $database): bool;
    
    /**
     * Gets last error information.
     */
    abstract public function getLastError(): ?array;
    
}