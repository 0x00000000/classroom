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
    
    /** 
     * Adds common varibles to page's view.
     */
    protected function setPageViewVariables(): void {
        parent::setPageViewVariables();
        
        $this->getPageView()->set('bodyClass', 'teacher');
    }
    
    protected function addJsAndCssFiles() {
        parent::addJsAndCssFiles();
        
        $this->addCssFile('/css/vendor/content-tools/content-tools.min.css');
        $this->addJsFile('/js/vendor/content-tools/content-tools.js');
        $this->addJsFile('/js/content-tools-editor.js');
    }
    
}
