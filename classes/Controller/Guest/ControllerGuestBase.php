<?php

declare(strict_types=1);

namespace Classroom\Controller\Guest;

use Classroom\Controller\ControllerBase;

abstract class ControllerGuestBase extends ControllerBase {
    
    /**
     * Adds common varibles to page's view.
     */
    protected function setPageViewVariables(): void {
        parent::setPageViewVariables();
        
        $this->getPageView()->set('bodyClass', 'guest');
    }
    
}
