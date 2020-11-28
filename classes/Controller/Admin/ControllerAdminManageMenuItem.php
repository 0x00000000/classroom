<?php

declare(strict_types=1);

namespace Classroom\Controller\Admin;

class ControllerAdminManageMenuItem extends ControllerAdminManageBase {
    
    /**
     * @var string $_modelName Name of manaaged model class.
     */
    protected $_modelName = 'MenuItem';
    
    /**
     * @var array $_sortingList Default sorting list.
     */
    protected $_sortingList = array('menuId' => 'asc', 'link' => 'ask');
    
    /**
     * @var array $_innerUrl Inner url to controller's root page. Should be started from '/'.
     */
    protected $_innerUrl = '/admin/menuItem';
    
    protected function setContentViewVariables() {
        parent::setContentViewVariables();
        
        $condition = array();
        $menuIdValues = $this->getFkValues('menuId', $condition, 'caption');
        $this->getView()->set('menuIdValues', $menuIdValues);
        
    }
    
}
