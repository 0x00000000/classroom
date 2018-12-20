<?php

declare(strict_types=1);

namespace classroom;

include_once('ModelDatabase.php');

/**
 * Model log.
 * Important messages, notices and errors can be saved as logs.
 * 
 * @property string|null $id Log's id.
 * @property string|null $requestId Current request's id.
 * @property int|null $level Log's level.
 * @property string|null $message Log's message.
 * @property string|null $description Log's description.
 * @property array|null $data Log's additional data.
 * @property int|null $code Php error level constant.
 * @property string|null $file File's path.
 * @property string|null $line Line of the file.
 * @property string|null $url Url.
 */
class ModelLog extends ModelDatabase {
    /**
     * Log's levels.
     */
    public const LEVEL_CRITICAL = 1;
    public const LEVEL_ERROR = 2;
    public const LEVEL_WARNING = 4;
    public const LEVEL_NOTICE = 8;
    
    protected $_id = null;
    protected $_requestId = null;
    protected $_level = null;
    protected $_message = null;
    protected $_description = null;
    protected $_data = null;
    protected $_code = null;
    protected $_file = null;
    protected $_line = null;
    protected $_url = null;
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'log';
    
    /**
     * @var Request $_request Request object.
     */
    protected $_request = null;
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
        
        $this->addHiddenProperty('_request');
    }
    
    /**
     * Sets log information from params to this object.
     */
    public function create(
        int $level, string $message, string $description = null,
        array $data = null, int $code = null,
        string $file = null, int $line = null, string $url = null
    ): bool {
        $result = false;
        
        if ($this->getRequest()) {
            if (! $this->getRequest()->getCurrentRequest()->id) {
                $this->getRequest()->getCurrentRequest()->save();
            }
        }
        
        if ($this->checkLevel($level) && strlen($message)) {
            if ($this->getRequest()) {
                $this->requestId = $this->getRequest()->getCurrentRequest()->id;
            } else {
                $this->requestId = null;
            }
            
            $this->level = $level;
            $this->message = $message;
            $this->description = $description;
            $this->data = $data;
            $this->code = $code;
            $this->file = $file;
            $this->line = $line;
            $this->url = $url;
            
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Creates critical error log.
     */
    public function createCritical(
        string $message, string $description = null,
        array $data = null, int $code = null,
        string $file = null, int $line = null, string $url = null
    ): bool {
        if ($code === null) {
            $code = E_USER_ERROR;
        }
        return $this->create(self::LEVEL_CRITICAL, $message, $description, $data, $code, $file, $line, $url);
    }
    
    /**
     * Creates error log.
     */
    public function createError(
        string $message, string $description = null,
        array $data = null, int $code = null,
        string $file = null, int $line = null, string $url = null
    ): bool {
        if ($code === null) {
            $code = E_USER_ERROR;
        }
        return $this->create(self::LEVEL_ERROR, $message, $description, $data, $code, $file, $line, $url);
    }
    
    /**
     * Creates warning log.
     */
    public function createWarning(
        string $message, string $description = null,
        array $data = null, int $code = null,
        string $file = null, int $line = null, string $url = null
    ): bool {
        if ($code === null) {
            $code = E_USER_WARNING;
        }
        return $this->create(self::LEVEL_WARNING, $message, $description, $data, $code, $file, $line, $url);
    }
    
    /**
     * Creates notice log.
     */
    public function createNotice(
        string $message, string $description = null,
        array $data = null, int $code = null,
        string $file = null, int $line = null, string $url = null
    ): bool {
       if ($code === null) {
            $code = E_USER_NOTICE ;
        }
        return $this->create(self::LEVEL_NOTICE, $message, $description, $data, $code, $file, $line, $url);
    }
    
    /**
     * Checks if log's level is correct.
     */
    protected function checkLevel(int $level): bool {
        $result = false;
        
        if ($level) {
            if (
                $level === self::LEVEL_CRITICAL
                || $level === self::LEVEL_ERROR
                || $level === self::LEVEL_WARNING
                || $level === self::LEVEL_NOTICE
            ) {
                $result = true;
            }
        }
        
        return $result;
    }
    
    /**
     * Set log's message property.
     */
    protected function setMessage(string $value): void {
        $maxMessageLength = 255;
        
        if ($value) {
            if (strlen($value) > $maxMessageLength) {
                $value = substr($value, 0, $maxMessageLength);
            }
        }
        
        $this->_message = $value;
    }
    
    /**
     * Set log's url property.
     */
    protected function setUrl(string $value): void {
        $maxUrlLength = 255;
        
        if ($value) {
            if (strlen($value) > $maxUrlLength) {
                $value = substr($value, 0, $maxUrlLength);
            }
        }
        
        $this->_url = $value;
    }
    
    /**
     * Gets log's data property.
     */
    public function getData() {
        return $this->_data ? json_decode($this->_data, true) : null;
    }
    
    /**
     * Sets log's data property.
     */
    public function setData($value): void {
        $this->_data = json_encode($value);
    }
    
    /**
     * Gets request model.
     */
    protected function getRequest(): Request {
        return $this->_request;
    }
    
    /**
     * Sets request model.
     */
    public function setRequest(Request $request): bool {
        $result = false;
        
        if (is_object($request) && $request instanceof Request) {
            $this->_request = $request;
            $result = true;
        }
        
        return $result;
    }
    
}