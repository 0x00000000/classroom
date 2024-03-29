<?php

declare(strict_types=1);

namespace Classroom\Module\Config;

/**
 * Stores configuration data for other modules.
 */
abstract class ConfigAbstract {
    
    /**
     * Gets data from configuration.
     */
    abstract public function get(string $section, string $name = null);
    
}