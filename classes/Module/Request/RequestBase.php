<?php

declare(strict_types=1);

namespace classroom;

include_once('Request.php');

/**
 * Allows to get current request information.
 * 
 * @property string|null $url Request's url (readonly).
 * @property array|null $get Request's get data (readonly).
 * @property array|null $post Request's post data (readonly).
 * @property array|null $session Request's session data (readonly).
 * @property array|null $headers Request's headers (readonly).
 * @property string|null $ip User ip (readonly).
 * @property string|null $userAgent Rser agent infoimation (readonly).
 */
abstract class RequestBase extends Request {
    
    /**
     * Current request.
     */
    protected $_currentRequest = null;
    
    /**
     * Class constructor.
     */
    public function __construct() {
        $this->create();
    }
    
    /**
     * Gets current request.
     */
    public function getCurrentRequest(): ModelRequest {
        return $this->_currentRequest;
    }
    
    /**
     * Some properties may be get by this magic meghod.
     * 
     * @property string|null $url Request's url (readonly).
     * @property array|null $get Request's get data (readonly).
     * @property array|null $post Request's post data (readonly).
     * @property array|null $session Request's session data (readonly).
     * @property array|null $headers Request's headers (readonly).
     * @property string|null $ip User ip (readonly).
     * @property string|null $userAgent Rser agent infoimation (readonly).
     */
    public function __get(string $name) {
        $result = null;
        
        switch ($name) {
            case 'url':
                $result = $this->getCurrentRequest()->url;
                break;
            case 'get':
                $result = $this->getCurrentRequest()->get;
                break;
            case 'post':
                $result = $this->getCurrentRequest()->post;
                break;
            case 'session':
                $result = $this->getCurrentRequest()->session;
                break;
            case 'headers':
                $result = $this->getCurrentRequest()->headers;
                break;
            case 'ip':
                $result = $this->getCurrentRequest()->ip;
                break;
            case 'userAgent':
                $result = $this->getCurrentRequest()->userAgent;
                break;
            default:
                $result = null;
                break;
        }
        
        return $result;
    }
    
    /**
     * Creates current reqeust. 
     */
    protected function create() {
        $this->_currentRequest = Factory::instance()->createModelRequest();
        
        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $this->_currentRequest->url = $_SERVER['REQUEST_URI'];
        }
        $this->_currentRequest->get = isset($_GET) ? $_GET : array();
        $this->_currentRequest->post = isset($_POST) ? $_POST : array();
        $this->_currentRequest->session = isset($_SESSION) ? $_SESSION : array();
        if (function_exists('getallheaders')) {
            $this->_currentRequest->headers = getallheaders();
        } else {
            $this->_currentRequest->headers = array();
        }
        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $this->_currentRequest->ip = $_SERVER['REMOTE_ADDR'];
        }
        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            $this->_currentRequest->userAgent = $_SERVER['HTTP_USER_AGENT'];
        }
        
        $this->_currentRequest->save();
    }
    
}
