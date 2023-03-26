<?php

declare(strict_types=1);

namespace Classroom\Model;

use Classroom\System\FileSystem;

use Classroom\Module\Config\Config;

/**
 * Model content image.
 * 
 * @property sring $id id.
 * @property string $userId User, uploaded image.
 * @property string $filepath Link to image. Should be child of 'public' directory.
 * @property string $size Image size (in bytes).
 * @property string $type Image mime type.
 * @property string $width Images width.
 * @property string $height Image height.
 * @property string $created Create date.
 * @property string $updated Update date.
 * @property bool $disabled Is user disabled.
 * @property bool $deleted Is user deleted.
 */
class ModelContentImage extends ModelDatabase {
    /**
     * @var strign $_table Name of database table.
     */
    protected $_table = 'content_image';
    
    /**
     * @var array $_propertiesList List of properties.
     */
    protected $_propertiesList = array(
        array('name' => 'id'),
        array('name' => 'userId', 'type' => self::TYPE_FK, 'fkModelName' => 'User'),
        array('name' => 'filepath'),
        array('name' => 'size'),
        array('name' => 'type'),
        array('name' => 'width'),
        array('name' => 'height'),
        array('name' => 'created'),
        array('name' => 'updated'),
        array('name' => 'disabled', 'type' => self::TYPE_BOOL),
        array('name' => 'deleted', 'type' => self::TYPE_BOOL, 'skipControl' => true),
    );
    
    /**
     * @var array $_allowedExtensionsList Allowed file extensions.
     */
    private $_allowedExtensionsList = array('jpg','jpeg','png','gif','bmp',);
    
    /**
     * @var array|null $_fileInfo Information about uploading file.
     */
    private $_fileInfo = null;
    
    /**
     * @var string|null $_rootUrl Url to site root.
     */
    private $_rootUrl = null;
    
    /**
     * @var strign|null $_uploadPath Images upload path.
     */
    private $_uploadPath = null;
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
        
        $this->_uploadPath = Config::instance()->get('contentImage', 'uploadPath');
    }
    
    public function setFileInfo(array $fileInfo): bool {
        if (
            ! empty($fileInfo['tmp_name'])
            && ! empty($fileInfo['name'])
            && ! empty($fileInfo['type'])
            && file_exists($fileInfo['tmp_name'])
        ) {
            $this->_fileInfo = $fileInfo;
            $result = true;
        } else {
            $result = false;
        }
        
        return $result;
    }
    
    public function getFileInfo(): ?array {
        return $this->_fileInfo;
    }
    
    /**
     * Saves object's data to database.
     */
    public function save(): ?string {
        $result = null;
        
        $fileInfo = $this->getFileInfo();
        if ($fileInfo) {
            $tmpName = $fileInfo['tmp_name'];
        
            $ext = strtolower(substr(strrchr($fileInfo['name'], '.'), 1));
            $size = getimagesize($tmpName);
            if($size && in_array($ext, $this->_allowedExtensionsList)) {
                if (! $this->id) {
                    $this->created = $this->getDate();
                    parent::save();
                }
                
                if ($this->id) {
                    $filename = $this->id . '.' . $ext;
                    $path = $this->getUploadPath() . FileSystem::getDS() . $filename;
                    $fullPath = FileSystem::getRoot() . $path;
                    
                    if (FileSystem::moveUploadedFile($tmpName, $fullPath)) {
                        $this->filepath = $path;
                        $this->size = FileSystem::getFileSize($fullPath);
                        $this->type = $size['mime'] ? $size['mime'] : $fileInfo['type'];
                        $this->width = $size[0];
                        $this->height = $size[1];
                        $this->updated = $this->getDate();
                        $result = parent::save();
                    } else {
                        $result = null;
                    }
                } else {
                    $result = null;
                }
            } else {
                $result = null;
            }
        
        } else {
            $result = parent::save();
        }
        
        return $result;
        
    }
    
    public function getUri(): ?string {
        if ($this->filepath) {
            $fileUri = preg_replace('|^/public/|', '/', $this->filepath);
            $fileUri = $this->getRootUrl() . $fileUri;
            return $fileUri;
        } else {
            $fileUri = null;
        }
        
        return $fileUri;
    }
    
    public function setRootUrl(string $rootUrl) {
        $this->_rootUrl = $rootUrl;
    }
    
    private function getRootUrl(): ?string {
        return $this->_rootUrl;
    }
    
    public function setUploadPath(string $path) {
        $this->_uploadPath = $path;
    }
    
    public function getUploadPath(): ?string {
        return $this->_uploadPath;
    }
    
    private function getDate(): string {
        return date('Y-m-d H:i:s');
    }
    
}

