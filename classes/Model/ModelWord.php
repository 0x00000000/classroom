<?php

declare(strict_types=1);

namespace classroom;

include_once('ModelDatabase.php');

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
 */
class ModelWord extends ModelDatabase {
    /**
     * Default url if $_SERVER['SERVER_NAME'] is not set.
     */
    public const UNKNOWN_SERVER_NAME = 'UNKNOWN_SERVER_NAME';
    
    protected $_id = null;
    protected $_english = null;
    protected $_russian = null;
    protected $_transcription = null;
    protected $_audio = null;
    protected $_audioFileName = null;
    protected $_audioFileType = null;
    protected $_audioSource = null;
    protected $_image = null;
    protected $_imageFileName = null;
    protected $_imageFileType = null;
    protected $_imageSource = null;
    protected $_partOfSpeech = null;
    protected $_isPlural = null;
    protected $_isCountable = null;
    protected $_isPhrase = null;
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'word';
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
    }
    
    protected function getAudio(): bool {
        return ($this->_audio === '1');
    }
    
    protected function setAudio(?bool $value): void {
        if ($value) {
            $valueAsString = '1';
        } else {
            $valueAsString = '0';
        }
        $this->_audio = $valueAsString;
    }
    
    protected function getImage(): bool {
        return ($this->_image === '1');
    }
    
    protected function setImage(?bool $value): void {
        if ($value) {
            $valueAsString = '1';
        } else {
            $valueAsString = '0';
        }
        $this->_image = $valueAsString;
    }
    
    protected function getIsPlural(): bool {
        return ($this->_isPlural === '1');
    }
    
    protected function setIsPlural(?bool $value): void {
        if ($value) {
            $valueAsString = '1';
        } else {
            $valueAsString = '0';
        }
        $this->_isPlural = $valueAsString;
    }
    
    protected function getIsCountable(): bool {
        return ($this->_isCountable === '1');
    }
    
    protected function setIsCountable(?bool $value): void {
        if ($value) {
            $valueAsString = '1';
        } else {
            $valueAsString = '0';
        }
        $this->_isCountable = $valueAsString;
    }
    
    protected function getIsPhrase(): bool {
        return ($this->_isPhrase === '1');
    }
    
    protected function setIsPhrase(?bool $value): void {
        if ($value) {
            $valueAsString = '1';
        } else {
            $valueAsString = '0';
        }
        $this->_isPhrase = $valueAsString;
    }
    
}
