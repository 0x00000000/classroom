<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerTeacherManageBase.php');

class ControllerTeacherManageLessonTemplate extends ControllerTeacherManageBase {
    
    /**
     * @var string $_modelName Name of manaaged model class.
     */
    protected $_modelName = 'LessonTemplate';
    
    /**
     * @var array $_modelControlsList Defines controls for model properties.
     * 
     * propertyName => controlType
     */
    protected $_modelControlsList = array(
        'teacherId' => self::CONTROL_NONE,
        'content' => self::CONTROL_HTML,
    );
    
    /**
     * @var array $_innerUrl Inner url to controller's root page. Should be started from '/'.
     */
    protected $_innerUrl = '/teacher/lessonTemplate';
    
    protected function setPropertiesFromPost(ModelDatabase $model): bool {
        $result = parent::setPropertiesFromPost($model);
        
        if ($result) {
            $model->teacherId = $this->getAuth()->getUser()->id;
        }
        
        return $result;
    }
    
    protected function innerActionView() {
        $this->_conditionsList['teacherId'] = $this->getAuth()->getUser()->id;
        
        return parent::innerActionView();
    }
    
    protected function innerActionEdit() {
        $this->_conditionsList['teacherId'] = $this->getAuth()->getUser()->id;
        
        return parent::innerActionEdit();
    }
    
    protected function innerActionList() {
        $this->_conditionsList['teacherId'] = $this->getAuth()->getUser()->id;
        
        return parent::innerActionList();
    }
    
}
