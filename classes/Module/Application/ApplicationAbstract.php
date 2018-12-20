<?php

declare(strict_types=1);

namespace classroom;

/**
 * Facade for other modules.
 * Runs application.
 */
abstract class ApplicationAbstract {
    
    /**
     * Runs application.
     */
    abstract public function run(): void;
    
}