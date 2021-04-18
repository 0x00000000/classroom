<?php

declare(strict_types=1);

namespace Classroom\Controller\Teacher;

use Classroom\Module\Factory\Factory;
use Classroom\Module\NicImageUploader\NicImageUploader;

use Classroom\Controller\ControllerContentImageBase;

class ControllerTeacherContentImage extends ControllerContentImageBase {
    
    protected function actionIndex() {
        
        $this->setAjaxMode(true);
        
        if (! $this->getAuth()->isTeacher()) {
            $nicImageUploader = Factory::instance()->createModule('NicImageUploader');
            return json_encode($nicImageUploader->getAuthError());
        } else {
            return parent::actionIndex();
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
