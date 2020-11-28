<?php

declare(strict_types=1);

namespace Classroom\Controller\Admin;

use Classroom\Controller\ControllerBase;

abstract class ControllerAdminBase extends ControllerBase {
    
    /**
     * Executes before controller action.
     */
    protected function before(): void {
        if (! $this->getAuth()->isAdmin()) {
            $this->redirect($this->getAuthUrl());
        }
    }
    
    /** 
     * Adds common varibles to page's view.
     */
    protected function setPageViewVariables(): void {
        parent::setPageViewVariables();
        
        $this->getPageView()->set('bodyClass', 'admin');
    }
    
}
