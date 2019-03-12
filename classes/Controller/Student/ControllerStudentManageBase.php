<?php

declare(strict_types=1);

namespace classroom;

Factory::instance()->loadController('ControllerManageBase');

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
    
}
