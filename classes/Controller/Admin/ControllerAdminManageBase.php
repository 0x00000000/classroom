<?php

declare(strict_types=1);

namespace classroom;

Factory::instance()->loadController('ControllerManageBase');

class ControllerAdminManageBase extends ControllerManageBase {
    
    /**
     * Execute controller action.
     */
    public function execute(string $action): void {
        if (! $this->getAuth() || ! $this->getAuth()->isAdmin()) {
            $this->redirect($this->getAuthUrl());
        }
        
        parent::execute($action);
    }
    
    /** 
     * Adds common varibles to page's view.
     */
    protected function setPageViewVariables(): void {
        parent::setPageViewVariables();
        
        $this->getPageView()->set('bodyClass', 'admin');
    }
    
    protected function addJsAndCssFiles() {
        parent::addJsAndCssFiles();
        
        $this->addCssFile('/css/vendor/content-tools/content-tools.min.css');
        $this->addJsFile('/js/vendor/content-tools/content-tools.js');
        $this->addJsFile('/js/content-tools-editor.js');
    }
    
}
