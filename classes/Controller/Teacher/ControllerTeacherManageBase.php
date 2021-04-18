<?php

declare(strict_types=1);

namespace Classroom\Controller\Teacher;

use Classroom\Controller\ControllerManageBase;

class ControllerTeacherManageBase extends ControllerManageBase {
    
    /**
     * Execute controller action.
     */
    public function execute(string $action): void {
        if (! $this->getAuth() || ! $this->getAuth()->isTeacher()) {
            $this->redirect($this->getAuthUrl());
        }
        
        parent::execute($action);
    }
    
    /** 
     * Adds common varibles to page's view.
     */
    protected function setPageViewVariables(): void {
        parent::setPageViewVariables();
        
        $this->getPageView()->set('bodyClass', 'teacher');
    }
    
}
