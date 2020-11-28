<?php

declare(strict_types=1);

namespace Classroom\Controller\Admin;

class ControllerAdminIndex extends ControllerAdminBase {
    
    protected function actionIndex() {
        $this->redirect($this->getRootUrl() . '/admin/page');
    }
    
}
