<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerStudentManageBase.php');

class ControllerStudentManageLesson extends ControllerStudentManageBase {
    
    /**
     * @var string $_modelName Name of manaaged model class.
     */
    protected $_modelName = 'Lesson';
    
    /**
     * @var array $_conditionsList Default conditions list.
     */
    protected $_conditionsList = array('deleted' => false, 'disabled' => false,);
    
    /**
     * @var array $_modelControlsList Defines controls for model properties.
     * 
     * propertyName => controlType
     */
    protected $_modelControlsList = array(
        'id' => self::CONTROL_NONE,
        'lessonTemplateId' => self::CONTROL_NONE,
        'teacherId' => self::CONTROL_NONE,
        'studentId' => self::CONTROL_NONE,
        'keywords' => self::CONTROL_NONE,
        'disabled' => self::CONTROL_NONE,
        'deleted' => self::CONTROL_NONE,
    );
    
    /**
     * @var array $_innerUrl Inner url to root page. Should started from '/'.
     */
    protected $_innerUrl = '/student/lesson';
    
    /**
     * @var array $_templateNames View templates' names.
     */
    protected $_templateNames = array(
        'list' => 'Student/ManageLesson/list',
        'view' => 'Student/ManageLesson/view',
    );
    
    protected function setPropertiesFromPost(ModelDatabase $model): bool {
        return false;
    }
    
    protected function setContentViewVariables() {
        parent::setContentViewVariables();
        
        $user = $this->getAuth()->getUser();
        
        $condition = array();
        $teacherIdValues = $this->getFkValues('teacherId', $condition, 'name');
        $this->getView()->set('teacherIdValues', $teacherIdValues);
    }
    
    protected function actionIndex() {
        
        $get = $this->getRequest()->get;
        if (array_key_exists('action', $get)) {
            $this->_action = $get['action'];
        }
        if (array_key_exists('id', $get)) {
            $this->_id = $get['id'];
        }
        
        if ($this->_action === 'view' && $this->_id) {
            $content = $this->innerActionView();
        } else if ($this->_action === 'list') {
            $content = $this->innerActionList();
        } else if (empty($this->_action)) {
            $content = $this->innerActionList();
        } else {
            $content = '';
            $this->send404();
        }
        
        return $content;
    }
    
}
