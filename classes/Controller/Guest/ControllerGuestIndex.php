<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerGuestBase.php');

class ControllerGuestIndex extends ControllerGuestBase {
    
    protected function actionIndex() {
        $view = Factory::instance()->createView();
        $view->setTemplate('Guest/index');
        
        $content = $view->render();
        
        return $content;
    }
    
}
