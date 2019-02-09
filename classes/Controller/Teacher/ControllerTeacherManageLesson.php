<?php

declare(strict_types=1);

namespace classroom;

Factory::instance()->loadController('ControllerManageBase');

class ControllerTeacherManageLesson extends ControllerManageBase {
    
    /**
     * @var string $_modelName Name of manaaged model class.
     */
    protected $_modelName = 'ModelLesson';
    
}
