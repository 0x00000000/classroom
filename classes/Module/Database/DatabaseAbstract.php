<?php

declare(strict_types=1);

namespace classroom;

/**
 * Allow get and save data from/in database.
 */
abstract class DatabaseAbstract {
    
    /**
     * Gets data by id.
     */
    abstract public function getById(string $table, string $pk, string $primaryKey = 'id'): ?array;
    
    /**
     * Gets data for several records.
     */
    abstract public function getList(
        string $table, array $conditionsList,
        ?int $limit, ?int $offset
    ): array;
    
    /**
     * Gets count of records.
     */
    abstract public function getCount(string $table, array $conditionsList): int;
    
    /**
     * Saves the record in the database.
     */
    abstract public function addRecord(string $table, array $data): ?string;
    
    /**
     * Updates the record in the database.
     */
    abstract public function updateRecord(string $table, array $data, string $primaryKey = 'id'): ?string;
    
    /**
     * Deletes the record in the database.
     */
    abstract public function deleteRecord(string $table, string $pk, string $primaryKey = 'id'): ?string;
    
    /**
     * Gets last error information.
     */
    abstract public function getLastError(): ?array;
    
    /**
     * Escapes string for safe using in sql statements.
     */
    abstract protected function escape(string $value): string;
    
}