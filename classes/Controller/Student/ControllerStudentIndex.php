<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerStudentBase.php');

class ControllerStudentIndex extends ControllerStudentBase {
    
    protected function actionIndex() {
        $content = 'Index page.';
        
        return $content;
    }
    
}
