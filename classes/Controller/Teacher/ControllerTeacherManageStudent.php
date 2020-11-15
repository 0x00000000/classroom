<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerTeacherManageBase.php');

class ControllerTeacherManageStudent extends ControllerTeacherManageBase {
    
    /**
     * @var string $_modelName Name of manaaged model class.
     */
    protected $_modelName = 'User';
    
    /**
     * @var array $_sortingList Default sorting list.
     */
    protected $_sortingList = array('disabled' => 'asc', 'name' => 'asc');
    
    /**
     * @var array $_modelControlsList Defines controls for model properties.
     * 
     * propertyName => controlType
     */
    protected $_modelControlsList = array(
        'password' => self::CONTROL_PASSWORD,
        'isAdmin' => self::CONTROL_NONE,
        'isTeacher' => self::CONTROL_NONE,
        'isStudent' => self::CONTROL_NONE,
    );
    
    /**
     * @var array $_innerUrl Inner url to controller's root page. Should be started from '/'.
     */
    protected $_innerUrl = '/teacher/student';
    
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
            $model->isAdmin = false;
            $model->isTeacher = false;
            $model->isStudent = true;
            
            $user = $this->getAuth()->getUser();
            if ($model->setTeacher($user)) {
                $result = true;
            } else {
                $result = false;
            }
        }
        
        return $result;
    }
    
    protected function innerActionList() {
        if (array_key_exists('list', $this->_templateNames)) {
            $this->getView()->setTemplate($this->_templateNames['list']);
        }
        
        if ($this->_id) {
            $currentPage = (int) $this->_id;
            if ($currentPage <= 0) {
                $currentPage = 1;
            }
        } else {
            $currentPage = 1;
        }
        
        $user = $this->getAuth()->getUser();
        $itemsList = $user->getStudentsList(
            $this->_itemsPerPage,
            ((int) $currentPage - 1) * (int) $this->_itemsPerPage,
            $this->_sortingList
            
        );
        
        $itemsCount = $user->getStudentsCount();
        
        $pagesList = array();
        if ($itemsCount > 1) {
            $pagesCount = floor(($itemsCount - 1) / $this->_itemsPerPage);
            for ($i = 1; $i <= $pagesCount; $i++) {
                $pagesList[] = (string) $i;
            }
        } else {
            $pagesCount = 1;
        }
        
        $this->getView()->set('itemsList', $itemsList);
        $this->getView()->set('currentPage', $currentPage);
        $this->getView()->set('pagesCount', $pagesCount);
        $this->getView()->set('pagesList', $pagesList);
        
        $content = $this->getView()->render();
        
        return $content;
    }
    
    protected function innerActionView() {
        $this->_conditionsList['teacherId'] = $this->getAuth()->getUser()->id;
        
        return parent::innerActionView();
    }
    
    protected function innerActionEdit() {
        $this->_conditionsList['teacherId'] = $this->getAuth()->getUser()->id;
        
        return parent::innerActionEdit();
    }
    
}
