<?php

namespace classroom;

$ds = DIRECTORY_SEPARATOR;
$filename = dirname(dirname(__FILE__)) . $ds . 'classes' . $ds . 'System' . $ds . 'Core.php';

if (is_file($filename)) {
    
    include_once($filename);
    
    Core::setApplicationType('Client');
    
    $application = Factory::instance()->createApplication();
    
    $application->run();
    
} else {
    
    echo 'Application error.';
    exit;
    
}
