<?php

declare(strict_types=1);

namespace Classroom\Model;

/**
 * Model lesson.
 * 
 * @property string|null $id Id.
 * @property string|null $lessonTemplateId Lesson's template id. 
 * @property string|null $teacherId Teacher's id.
 * @property string|null $studentId Student id.
 * @property string|null $caption Caption.
 * @property string|null $subject Subject.
 * @property string|null $keywords Keywords.
 * @property string|null $content Content.
 * @property bool $disabled Is user disabled.
 * @property bool $deleted Is user deleted.
 */
class ModelLesson extends ModelDatabase {
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'lesson';
    
    /**
     * @var array $_propertiesList List of properties.
     */
    protected $_propertiesList = array(
        array('name' => 'id'),
        array('name' => 'lessonTemplateId', 'type' => self::TYPE_FK, 'fkModelName' => 'LessonTemplate'),
        array('name' => 'teacherId', 'type' => self::TYPE_FK, 'fkModelName' => 'User'),
        array('name' => 'studentId', 'type' => self::TYPE_FK, 'fkModelName' => 'User'),
        array('name' => 'caption'),
        array('name' => 'subject'),
        array('name' => 'keywords'),
        array('name' => 'content'),
        array('name' => 'disabled', 'type' => self::TYPE_BOOL),
        array('name' => 'deleted', 'type' => self::TYPE_BOOL, 'skipControl' => true),
    );
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
    }
    
}
