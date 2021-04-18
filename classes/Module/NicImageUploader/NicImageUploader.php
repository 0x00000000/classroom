<?php

declare(strict_types=1);

namespace Classroom\Module\NicImageUploader;

use Classroom\System\FileSystem;

use Classroom\Module\Auth\Auth;
use Classroom\Module\Config\Config;
use Classroom\Module\Factory\Factory;
use Classroom\Module\Request\Request;

use Classroom\Model\ModelContentImage;

/**
 * Images uploader for nicEdit.
 */
class NicImageUploader extends NicImageUploaderAbstract {
    
    /**
     * Request object.
     */
    protected $_request = null;
    
    /**
     * Auth object.
     */
    protected $_auth = null;
    
    /**
     * Uploads file.
     */
    public function upload(array $fileInfo): array {
        if(! $this->getAuth() || ! $this->getAuth()->getUser()) {
            return $this->getAuthError();
        }
        
        $contentImage = Factory::instance()->createModel('ContentImage');
        $contentImage->setRootUrl($this->getRequest()->getRootUrl());
        
        $contentImage->setUploadPath($this->getUploadPath());
        
        $contentImage->userId = $this->getAuth()->getUser()->id;
        $uploadPath = FileSystem::getRoot() . $this->getUploadPath();
        
        if(! is_dir($uploadPath) || ! is_writable($uploadPath)) {
            return $this->getErrorReoponse('Upload directory must exist and have write permissions on the server');
        }
        
        $file = $this->getRequest()->files['image'];
        
        if (! $contentImage->setFileInfo($file)) {
            return $this->getErrorReoponse('Must be less than '.$this->bytesToReadable($this->getMaxUploadSize()));
        }
        
        if (! $contentImage->save()) {
            return $this->getErrorReoponse('Must be less than '.$this->bytesToReadable($this->getMaxUploadSize()));
        }
        
        $status = array();
        $status['id'] = $contentImage->id;
        $status['datetime'] = strtotime($contentImage->updated);
        $status['type'] = $contentImage->type;
        $status['animated'] = false;
        $status['width'] = $contentImage->width;
        $status['height'] = $contentImage->height;
        $status['size'] = $contentImage->size;
        $status['link'] = $contentImage->getUri();
        
        return $this->getSuccessResponse($status);
    }
    
    /**
     * Gets request.
     */
    private function getRequest(): ?Request {
        return $this->_request;
    }
    
    /**
     * Sets request.
     */
    public function setRequest(Request $request): void {
        $this->_request = $request;
    }
    
    /**
     * Gets auth.
     */
    private function getAuth(): ?Auth {
        return $this->_auth;
    }
    
    /**
     * Sets auth.
     */
    public function setAuth(Auth $auth) {
        $this->_auth = $auth;
    }
    
    public function getAuthError(): array {
        return $this->getErrorReoponse('Auth error');
    }
    
    private function getErrorReoponse(string $msg): array {
        return $this->getResponse(array('error' => $msg, 'success' => false)); 
    }
    
    private function getSuccessResponse(array $status): array {
        return $this->getResponse(array('data' => $status));
    }
    
    private function getResponse(array $response): array {
        $default = array('success' => true, 'status' => 200);
        return $response + $default;
    }
    
    private function getMaxUploadSize() {
        $post_size = ini_get('post_max_size');
        $upload_size = ini_get('upload_max_filesize');
        if(! $post_size) $post_size = '8M';
        if(! $upload_size) $upload_size = '2M';
        
        return min($this->iniBytesFromString($post_size), $this->iniBytesFromString($upload_size));
    }

    private function iniBytesFromString($val) {
        $last = strtolower($val[strlen($val) - 1]);
        $val = (int) trim($val);
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    private function bytesToReadable($bytes) {
        if ($bytes <= 0) {
            return '0 Byte';
        }
       
        $convention = 1000;
        $s = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB');
        $e = floor(log($bytes, $convention));
        return round($bytes / pow($convention, $e), 2) . ' ' . $s[$e];
    }
    
    /**
     * Gets images upload path.
     */
    private function getUploadPath(): string {
        return Config::instance()->get('nicEdit', 'nicUploadPath');
    }

}
