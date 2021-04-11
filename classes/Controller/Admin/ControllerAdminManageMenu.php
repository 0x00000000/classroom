<?php

declare(strict_types=1);

namespace Classroom\Controller\Admin;

class ControllerAdminManageMenu extends ControllerAdminManageBase {
    
    /**
     * @var string $_modelName Name of managed model class.
     */
    protected $_modelName = 'Menu';
    
    /**
     * @var array $_innerUrl Inner url to controller's root page. Should be started from '/'.
     */
    protected $_innerUrl = '/admin/menu';
    
}
