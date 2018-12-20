<?php

declare(strict_types=1);

namespace classroom;

include_once('Application.php');

/**
 * Facade for other modules.
 * Runs application.
 */
abstract class ApplicationBase extends Application {
    
    /**
     * Class's constructor.
     */
    public function __construct() {
        
        error_reporting(E_ALL);
        
        $this->initSession();
        
    }
    
    /**
     * Runs application.
     */
    public function run(): void {
        
    }
    
    /**
     * Initialise sassion.
     */
    protected function initSession(): void {
        
        session_start();
        
    }
    
}
