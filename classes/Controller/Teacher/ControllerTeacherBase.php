<?php

declare(strict_types=1);

namespace classroom;

Factory::instance()->loadController('ControllerBase');

abstract class ControllerTeacherBase extends ControllerBase {
    
    /**
     * Executes before controller action.
     */
    protected function before(): void {
        if (! $this->getAuth()->isTeacher()) {
            $this->redirect($this->getAuthUrl());
        }
    }
    
}
