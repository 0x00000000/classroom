<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerClientBase.php');

class ControllerClient extends ControllerClientBase {
    
    protected function actionIndex() {
        $content = 'Index page.';
        
        return $content;
    }
    
}
