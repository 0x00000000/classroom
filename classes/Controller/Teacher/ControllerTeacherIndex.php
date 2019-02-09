<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerTeacherBase.php');

class ControllerTeacherIndex extends ControllerTeacherBase {
    
    protected function actionIndex() {
        $content = 'Index page.';
        
        return $content;
    }
    
}
