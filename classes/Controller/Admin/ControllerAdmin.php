<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerAdminBase.php');

class ControllerAdmin extends ControllerAdminBase {
    
    protected function actionIndex() {
        $content = 'Admin index page.';
        
        return $content;
    }
    
}
