<?php

declare(strict_types=1);

namespace Classroom\Controller\Test;

use Classroom\Controller\ControllerBase;

class ControllerTestIndex extends ControllerBase {
    
    protected function actionFillImages() {
        $body = 'Test contorller.';
        
        return $body;
    }
    
}
