<?php

declare(strict_types=1);

namespace classroom;

include_once(dirname(__FILE__) . '/../classes/System/Core.php');

class init {
    
    public static function init(): void {
        $testMode = true;
        Core::setApplicationType('Client', $testMode);
        
        $application = Factory::instance()->createApplication();
        
        $application->run();
    }
    
}

init::init();
