<?php

declare(strict_types=1);

namespace Classroom\Controller\Teacher;

use Classroom\Controller\ControllerBase;

abstract class ControllerTeacherBase extends ControllerBase {
    
    /**
     * Executes before controller action.
     */
    protected function before(): void {
        if (! $this->getAuth()->isTeacher()) {
            $this->redirect($this->getAuthUrl());
        }
    }
    
    /** 
     * Adds common varibles to page's view.
     */
    protected function setPageViewVariables(): void {
        parent::setPageViewVariables();
        
        $this->getPageView()->set('bodyClass', 'teacher');
    }
    
}
