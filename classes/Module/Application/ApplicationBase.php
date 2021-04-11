<?php

declare(strict_types=1);

namespace Classroom\Module\Application;

use Classroom\Module\Config\Config;

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
        ini_set('display_errors', '1');
        
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
        
        $sessionLifeTime = Config::instance()->get('site', 'sessionLifeTime');
        ini_set('session.gc_maxlifetime', $sessionLifeTime);
        ini_set('session.cookie_lifetime', $sessionLifeTime);
        
        session_start();
        
    }
    
}
