<?php

declare(strict_types=1);

namespace Classroom\Controller\Teacher;

class ControllerTeacherIndex extends ControllerTeacherBase {
    
    protected function actionIndex() {
        $this->redirect($this->getRootUrl() . '/teacher/activeLesson');
    }
    
}
