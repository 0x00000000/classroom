<?php

declare(strict_types=1);

namespace Classroom\Controller\Student;

use Classroom\Controller\ControllerBase;

abstract class ControllerStudentBase extends ControllerBase {
    
    /**
     * Executes before controller action.
     */
    protected function before(): void {
        if (! $this->getAuth()->isStudent()) {
            $this->redirect($this->getAuthUrl());
        }
    }
    
    /**
     * Adds common varibles to page's view.
     */
    protected function setPageViewVariables(): void {
        parent::setPageViewVariables();
        
        $this->getPageView()->set('bodyClass', 'student');
    }
        
}
    