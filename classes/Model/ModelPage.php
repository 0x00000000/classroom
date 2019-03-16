<?php

declare(strict_types=1);

namespace classroom;

include_once('ModelAccessRights.php');

/**
 * Model page.
 * 
 * @property string|null $id Id.
 * @property string|null $caption Caption.
 * @property string|null $url Url, started from "/" .
 * @property string|null $title Title. 
 * @property string|null $keywords Meta keywords. 
 * @property string|null $description Meta description.
 * @property string|null $content Page content (html).
 * @property bool $accessAdmin Access rights for viewing.
 * @property bool $accessTeacher Access rights for viewing.
 * @property bool $accessStudent Access rights for viewing.
 * @property bool $accessGuest Access rights for viewing.
 * @property bool $disabled Is user disabled.
 * @property bool $deleted Is user deleted.
 */
class ModelPage extends ModelAccessRights {
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'page';
    
    /**
     * @var array $_propertiesList List of properties.
     */
    protected $_propertiesList = array(
        array('name' => 'id'),
        array('name' => 'caption'),
        array('name' => 'url'),
        array('name' => 'title'),
        array('name' => 'keywords'),
        array('name' => 'description'),
        array('name' => 'content'),
        array('name' => 'accessAdmin', 'type' => self::TYPE_BOOL),
        array('name' => 'accessTeacher', 'type' => self::TYPE_BOOL),
        array('name' => 'accessStudent', 'type' => self::TYPE_BOOL),
        array('name' => 'accessGuest', 'type' => self::TYPE_BOOL),
        array('name' => 'disabled', 'type' => self::TYPE_BOOL),
        array('name' => 'deleted', 'type' => self::TYPE_BOOL, 'skipControl' => true),
    );
    
}
