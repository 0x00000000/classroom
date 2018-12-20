<?php

declare(strict_types=1);

namespace classroom;

Factory::instance()->loadController('ControllerBase');

class ControllerTest extends ControllerBase {
    
    protected function actionFillImages() {
        $body = '@@@';
        
        return $body;
    }
    
}
