<?php

declare(strict_types=1);

namespace Classroom\Controller\Admin;

use Classroom\Model\ModelDatabase;

class ControllerAdminManageTeacher extends ControllerAdminManageBase {
    
    /**
     * @var string $_modelName Name of manaaged model class.
     */
    protected $_modelName = 'User';
    
    /**
     * @var array $_conditionsList Default conditions list.
     */
    protected $_conditionsList = array('isTeacher' => true, 'isAdmin' => false, 'deleted' => false,);
    
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
    protected $_innerUrl = '/admin/teacher';
    
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
            $model->isTeacher = true;
            $model->isStudent = false;
        }
        
        return $result;
    }
    
}
