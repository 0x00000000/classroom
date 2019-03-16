<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerGuestBase.php');

class ControllerGuestIndex extends ControllerGuestBase {
    
    /**
     * @var string $_action Action paramether.
     */
    protected $_page = null;
    
    protected function actionIndex() {
        $get = $this->getRequest()->get;
        if (array_key_exists('page', $get)) {
            $this->_page = $get['page'];
        }
        
        $view = Factory::instance()->createView();
        if ($this->_page === 'test') {
            $view->setTemplate('Guest/test');
        } else {
            $view->setTemplate('Guest/index');
        }
        
        $content = $view->render();
        
        return $content;
    }
    
}
