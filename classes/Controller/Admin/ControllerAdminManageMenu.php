<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerAdminManageBase.php');

class ControllerAdminManageMenu extends ControllerAdminManageBase {
    
    /**
     * @var string $_modelName Name of manaaged model class.
     */
    protected $_modelName = 'Menu';
    
    /**
     * @var array $_innerUrl Inner url to controller's root page. Should be started from '/'.
     */
    protected $_innerUrl = '/admin/menu';
    
}
