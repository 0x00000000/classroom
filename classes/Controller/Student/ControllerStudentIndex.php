<?php

declare(strict_types=1);

namespace Classroom\Controller\Student;

class ControllerStudentIndex extends ControllerStudentBase {
    
    protected function actionIndex() {
        $this->redirect($this->getRootUrl() . '/student/lesson');
    }
    
}
