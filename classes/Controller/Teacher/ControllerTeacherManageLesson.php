<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerTeacherManageBase.php');

class ControllerTeacherManageLesson extends ControllerTeacherManageBase {
    
    /**
     * @var string $_modelName Name of manaaged model class.
     */
    protected $_modelName = 'Lesson';
    
    /**
     * @var array $_modelControlsList Defines controls for model properties.
     * 
     * propertyName => controlType
     */
    protected $_modelControlsList = array(
        'teacherId' => self::CONTROL_NONE,
        'content' => self::CONTROL_TEXTAREA,
    );
    
    /**
     * @var array $_innerUrl Inner url to controller's root page. Should be started from '/'.
     */
    protected $_innerUrl = '/teacher/lesson';
    
    /**
     * @var array $_templateNames View templates' names.
     */
    protected $_templateNames = array(
        'add' => 'Teacher/ManageLesson/add',
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
        parent::setContentViewVariables();
        
        $user = $this->getAuth()->getUser();
        
        $condition = array('teacherId' => $user->id);
        $lessonTemplateIdValues = $this->getFkValues('lessonTemplateId', $condition, 'caption');
        $this->getView()->set('lessonTemplateIdValues', $lessonTemplateIdValues);
        
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
            if ($model->lessonTemplateId) {
                $modelTemplate = Factory::instance()->createModel('LessonTemplate');
                if ($modelTemplate->loadByPk($model->lessonTemplateId)) {
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
    
}
