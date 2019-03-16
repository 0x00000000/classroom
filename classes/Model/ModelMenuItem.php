<?php

declare(strict_types=1);

namespace classroom;

include_once('ModelAccessRights.php');

/**
 * Model menu item.
 * 
 * @property string|null $id Id.
 * @property string|null $menuId Menu id.
 * @property string|null $caption Caption.
 * @property string|null $link Link.
 * @property bool $accessAdmin Access rights for viewing.
 * @property bool $accessTeacher Access rights for viewing.
 * @property bool $accessStudent Access rights for viewing.
 * @property bool $accessGuest Access rights for viewing.
 * @property int $position Sort position.
 * @property bool $disabled Is user disabled.
 * @property bool $deleted Is user deleted.
 */
class ModelMenuItem extends ModelAccessRights {
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'menu_item';
    
    /**
     * @var array $_propertiesList List of properties.
     */
    protected $_propertiesList = array(
        array('name' => 'id'),
        array('name' => 'menuId', 'type' => self::TYPE_FK, 'fkModelName' => 'Menu'),
        array('name' => 'caption'),
        array('name' => 'link'),
        array('name' => 'accessAdmin', 'type' => self::TYPE_BOOL),
        array('name' => 'accessTeacher', 'type' => self::TYPE_BOOL),
        array('name' => 'accessStudent', 'type' => self::TYPE_BOOL),
        array('name' => 'accessGuest', 'type' => self::TYPE_BOOL),
        array('name' => 'position'),
        array('name' => 'disabled', 'type' => self::TYPE_BOOL),
        array('name' => 'deleted', 'type' => self::TYPE_BOOL, 'skipControl' => true),
    );
    
}
