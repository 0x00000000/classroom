<?php

declare(strict_types=1);

namespace Classroom\Model;

/**
 * Model lesson.
 * 
 * @property string|null $id Log's id.
 * @property string|null $english
 * @property string|null $russian
 * @property string|null $transcription
 * @property bool $audio
 * @property string|null $audioFileName
 * @property string|null $audioFileType
 * @property string|null $audioSource
 * @property bool $image
 * @property string|null $imageFileName
 * @property string|null $imageFileType
 * @property string|null $imageSource
 * @property string|null $partOfSpeech
 * @property bool $isPlural
 * @property bool $isCountable
 * @property bool $isPhrase
 * @property bool $disabled Is user disabled.
 * @property bool $deleted Is user deleted.
 */
class ModelWord extends ModelDatabase {
    /**
     * Default url if $_SERVER['SERVER_NAME'] is not set.
     */
    public const UNKNOWN_SERVER_NAME = 'UNKNOWN_SERVER_NAME';
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'word';
    
    /**
     * @var array $_propertiesList List of properties.
     */
    protected $_propertiesList = array(
        array('name' => 'id'),
        array('name' => 'english'),
        array('name' => 'russian'),
        array('name' => 'transcription'),
        array('name' => 'audio', 'type' => self::TYPE_BOOL),
        array('name' => 'audioFileName'),
        array('name' => 'audioFileType'),
        array('name' => 'audioSource'),
        array('name' => 'image', 'type' => self::TYPE_BOOL),
        array('name' => 'imageFileName'),
        array('name' => 'imageFileType'),
        array('name' => 'imageSource'),
        array('name' => 'partOfSpeech'),
        array('name' => 'isPlural', 'type' => self::TYPE_BOOL),
        array('name' => 'isCountable', 'type' => self::TYPE_BOOL),
        array('name' => 'isPhrase', 'type' => self::TYPE_BOOL),
        array('name' => 'disabled', 'type' => self::TYPE_BOOL),
        array('name' => 'deleted', 'type' => self::TYPE_BOOL, 'skipControl' => true),
    );
}
