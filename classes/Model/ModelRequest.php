<?php

declare(strict_types=1);

namespace classroom;

include_once('ModelDatabase.php');

/**
 * Model request.
 * Gets and saves http request's data.
 * 
 * @property string|null $id Log's id.
 * @property string|null $url Request's url.
 * @property array|null $get Request's get data.
 * @property array|null $post Request's post data.
 * @property array|null $session Request's session data.
 * @property array|null $headers Request's headers.
 * @property string|null $ip User ip.
 * @property string|null $userAgent Rser agent infoimation.
 * @property string|null $info Addititional infoimation.
 */
class ModelRequest extends ModelDatabase {
    /**
     * Default request url if $_SERVER['REQUEST_URI'] is not set.
     */
    public const UNKNOWN_REQUEST_URI = 'UNKNOWN_REQUEST_URI';
    
    protected $_id = null;
    protected $_url = null;
    protected $_get = null;
    protected $_post = null;
    protected $_session = null;
    protected $_headers = null;
    protected $_ip = null;
    protected $_userAgent = null;
    protected $_info = null;
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'request';
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Sets current http request information to this object.
     */
    public function create() {
        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $this->_url = $_SERVER['REQUEST_URI'];
        } else {
            $this->_url = self::UNKNOWN_REQUEST_URI;
        }
        
        $this->_get = json_encode($_GET);
        $this->_post = json_encode($_POST);
        if (! empty($_SESSION)) {
            $this->_session = json_encode($_SESSION);
        }
        if (function_exists('getallheaders')) {
            $this->_headers = json_encode(getallheaders());
        }
        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $this->_ip = $_SERVER['REMOTE_ADDR'];
        }
        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            $this->_userAgent = $_SERVER['HTTP_USER_AGENT'];
        }
    }
    
    /**
     * Gets request's get data.
     */
    public function getGet(): ?array {
        return $this->_get ? json_decode($this->_get, true) : null;
    }
    
    /**
     * Sets request's set data.
     */
    public function setGet(?array $value): void {
        $this->_get = json_encode($value);
    }
    
    /**
     * Gets request's post data.
     */
    public function getPost(): ?array {
        return $this->_post ? json_decode($this->_post, true) : null;
    }
    
    /**
     * Sets request's post data.
     */
    public function setPost(?array $value): void {
        $this->_post = json_encode($value);
    }
    
    /**
     * Gets request's session data.
     */
    public function getSession(): ?array {
        return $this->_session ? json_decode($this->_session, true) : null;
    }
    
    /**
     * Sets request's session data.
     */
    public function setSession(?array $value): void {
        $this->_session = json_encode($value);
    }
    
    /**
     * Gets request's headers.
     */
    public function getHeaders(): ?array {
        return $this->_headers ? json_decode($this->_headers, true) : null;
    }
    
    /**
     * Sets request's headers.
     */
    public function setHeaders(?array $value): void {
        $this->_headers = json_encode($value);
    }
    
}