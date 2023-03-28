<?php

declare(strict_types=1);

namespace Classroom\Controller\Student;

use Classroom\Model\ModelDatabase;

class ControllerStudentManageHomework extends ControllerStudentManageBase {
    
    /**
     * @var string $_modelName Name of managed model class.
     */
    protected $_modelName = 'Homework';
    
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
        'homeworkTemplateId' => self::CONTROL_NONE,
        'teacherId' => self::CONTROL_NONE,
        'studentId' => self::CONTROL_NONE,
        'keywords' => self::CONTROL_NONE,
        'caption' => self::CONTROL_LABEL,
        'subject' => self::CONTROL_LABEL,
        'content' => self::CONTROL_HTML_SIMPLE_PANEL,
        'disabled' => self::CONTROL_NONE,
        'deleted' => self::CONTROL_NONE,
    );
    
    /**
     * @var array $_innerUrl Inner url to root page. Should started from '/'.
     */
    protected $_innerUrl = '/student/homework';
    
    /**
     * @var array $_templateNames Templates names.
     */
    protected $_templateNames = array(
        'list' => 'Student/ManageHomework/list',
        'edit' => 'Common/ManageBase/edit',
    );
    
    protected function actionIndex() {
        
        $get = $this->getRequest()->get;
        if (array_key_exists('action', $get)) {
            $this->_action = $get['action'];
        }
        if (array_key_exists('id', $get)) {
            $this->_id = $get['id'];
        }
        
        if ($this->_action === 'edit' && $this->_id) {
            $content = $this->innerActionEdit();
        } else if (empty($this->_action) || $this->_action === 'list') {
            $content = $this->innerActionList();
        } else {
            $content = '';
            $this->send404();
        }
        
        return $content;
    }
    
    protected function innerActionEdit() {
        $this->_conditionsList['studentId'] = $this->getAuth()->getUser()->id;
        
        return parent::innerActionEdit();
    }
    
    protected function innerActionList() {
        $this->_conditionsList['studentId'] = $this->getAuth()->getUser()->id;
        
        return parent::innerActionList();
    }
    
}
