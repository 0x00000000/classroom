<?php

declare(strict_types=1);

namespace Classroom\Controller\Teacher;

use Classroom\Module\Factory\Factory;

use Classroom\Model\ModelDatabase;

class ControllerTeacherManageHomework extends ControllerTeacherManageBase {
    
    /**
     * @var string $_modelName Name of managed model class.
     */
    protected $_modelName = 'Homework';
    
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
    protected $_innerUrl = '/teacher/homework';
    
    /**
     * @var array $_templateNames View templates' names.
     */
    protected $_templateNames = array(
        'add' => 'Common/ManageBase/add',
        'list' => 'Common/ManageBase/list',
        'view' => 'Common/ManageBase/view',
        'edit' => 'Common/ManageBase/edit',
    );
    
    protected function setPropertiesFromPost(ModelDatabase $model): bool {
        $result = parent::setPropertiesFromPost($model);
        
        if ($result) {
            $model->teacherId = $this->getAuth()->getUser()->id;
        }
        
        return $result;
    }
    
    protected function setContentViewVariables() {
        if (
            array_key_exists('action', $this->getRequest()->get) &&
            $this->getRequest()->get['action'] === 'add'
        ) {
            $this->_modelControlsList['caption'] = self::CONTROL_NONE;
            $this->_modelControlsList['subject'] = self::CONTROL_NONE;
            $this->_modelControlsList['keywords'] = self::CONTROL_NONE;
            $this->_modelControlsList['content'] = self::CONTROL_NONE;
            $this->_modelControlsList['deleted'] = self::CONTROL_NONE;
        }

        parent::setContentViewVariables();
        
        $user = $this->getAuth()->getUser();
        
        $condition = array('teacherId' => $user->id);
        $homeworkTemplateIdValues = $this->getFkValues('homeworkTemplateId', $condition, 'caption');
        $this->getView()->set('homeworkTemplateIdValues', $homeworkTemplateIdValues);
        
        $studentIdValues = array();
        $studentsList = $user->getStudentsList();
        if ($studentsList) {
            foreach ($studentsList as $student) {
                $studentIdValues[$student->id] = $student->name;
            }
        }
        $this->getView()->set('studentIdValues', $studentIdValues);
    }
    
    protected function innerActionDoAdd() {
        $model = Factory::instance()->createModel($this->_modelName);
        $canSave = $this->setPropertiesFromPost($model);
        if ($canSave) {
            $model->teacherId = $this->getAuth()->getUser()->id;
            if ($model->homeworkTemplateId) {
                $modelTemplate = Factory::instance()->createModel('HomeworkTemplate');
                if ($modelTemplate->loadByPk($model->homeworkTemplateId)) {
                    $model->caption = $modelTemplate->caption;
                    $model->subject = $modelTemplate->subject;
                    $model->keywords = $modelTemplate->keywords;
                    $model->content = $modelTemplate->content;
                }
            }
            if ($model->save()) {
                $this->setStashData('messageType', 'addedSuccessfully');
            } else {
                $this->setStashData('messageType', 'addingFailed');
            }
        } else {
            $this->setStashData('messageType', 'addingFailed');
        }
        
        $this->redirect($this->getBaseUrl() . '/edit/' . $model->getPk());
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
