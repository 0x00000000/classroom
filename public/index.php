<?php

declare(strict_types=1);

namespace Classroom;

use Classroom\Module\Factory\Factory;

$ds = DIRECTORY_SEPARATOR;
$filename = dirname(dirname(__FILE__)) . $ds . 'classes' . $ds . 'System' . $ds . 'Core.php';

if (is_file($filename)) {
    
    include_once($filename);
    
    \Classroom\System\Core::setApplicationType('Client');
    
    $application = Factory::instance()->createApplication();
    
    $application->run();
    
} else {
    
    echo 'Application error.';
    exit;
    
}
