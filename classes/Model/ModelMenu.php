<?php

declare(strict_types=1);

namespace Classroom\Model;

use Classroom\Module\Factory\Factory;

/**
 * Model menu.
 * 
 * @property string|null $id Id.
 * @property string|null $caption Caption.
 * @property string|null $variable Variable name.
 * @property bool $accessAdmin Access rights for viewing.
 * @property bool $accessTeacher Access rights for viewing.
 * @property bool $accessStudent Access rights for viewing.
 * @property bool $accessGuest Access rights for viewing.
 * @property bool $disabled Is user disabled.
 * @property bool $deleted Is user deleted.
 */
class ModelMenu extends ModelAccessRights {
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'menu';
    
    /**
     * @var array $_propertiesList List of properties.
     */
    protected $_propertiesList = array(
        array('name' => 'id'),
        array('name' => 'caption'),
        array('name' => 'variable'),
        array('name' => 'accessAdmin', 'type' => self::TYPE_BOOL),
        array('name' => 'accessTeacher', 'type' => self::TYPE_BOOL),
        array('name' => 'accessStudent', 'type' => self::TYPE_BOOL),
        array('name' => 'accessGuest', 'type' => self::TYPE_BOOL),
        array('name' => 'disabled', 'type' => self::TYPE_BOOL),
        array('name' => 'deleted', 'type' => self::TYPE_BOOL, 'skipControl' => true),
    );
    
    public function getItems(): array {
        $items = array();
        
        if ($this->getPk()) {
            $conditionsList = array('disabled' => false, 'delete' => false, 'menuId' => $this->getPk());
            $sortingList = array('position' => 'desc');
            $items = Factory::instance()->createModel('MenuItem')
                ->getModelsList($conditionsList, 0, 0, $sortingList);
        }
        
        return $items;
    }
}
