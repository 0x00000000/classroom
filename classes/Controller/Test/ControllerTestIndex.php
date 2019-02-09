<?php

declare(strict_types=1);

namespace classroom;

Factory::instance()->loadController('ControllerBase');

class ControllerTestIndex extends ControllerBase {
    
    protected function actionFillImages() {
        $body = 'Test contorller.';
        
        return $body;
    }
    
}
