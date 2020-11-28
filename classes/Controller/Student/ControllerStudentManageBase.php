<?php

declare(strict_types=1);

namespace Classroom\Controller\Student;

use Classroom\Controller\ControllerManageBase;

class ControllerStudentManageBase extends ControllerManageBase {
    
    /**
     * Execute controller action.
     */
    public function execute(string $action): void {
        if (! $this->getAuth() || ! $this->getAuth()->isStudent()) {
            $this->redirect($this->getAuthUrl());
        }
        
        parent::execute($action);
    }
    
    /**
     * Adds common varibles to page's view.
     */
    protected function setPageViewVariables(): void {
        parent::setPageViewVariables();
        
        $this->getPageView()->set('bodyClass', 'student');
    }
    
}
