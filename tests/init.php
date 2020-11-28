<?php

declare(strict_types=1);

namespace ClassroomTest;

use Classroom\Module\Factory\Factory;

include_once(dirname(__FILE__) . '/../classes/System/Core.php');

class init {
    
    public static function init(): void {
        $testMode = true;
        \Classroom\System\Core::setApplicationType('Client', $testMode);
        
        $application = Factory::instance()->createApplication();
        
        $application->run();
    }
    
}

init::init();
