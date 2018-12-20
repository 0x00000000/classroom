<?php

declare(strict_types=1);

namespace classroom;

include_once('ModelDatabase.php');

/**
 * Model user.
 * 
 * @property string|null $id User's id.
 * @property string $login User's login.
 * @property string $name User's name.
 * @property bool $isAdmin Is user admin.
 * @property bool $isTeacher Is user teacher.
 * @property bool $isStudent Is user student.
 * 
 * Property only to set:
 * @property string|null $password User's password (writeonly).
 */
class ModelUser extends ModelDatabase {
    /**
     * Default url if $_SERVER['SERVER_NAME'] is not set.
     */
    public const UNKNOWN_SERVER_NAME = 'UNKNOWN_SERVER_NAME';
    
    protected $_id = null;
    protected $_login = null;
    protected $_password = null;
    protected $_name = null;
    protected $_isAdmin = false;
    protected $_isTeacher = false;
    protected $_isStudent = false;
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'user';
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Check user's password.
     */
    public function loadByLogin(string $login): bool {
        return $this->loadByKey('login', $login);
    }
    
    /**
     * Load user from database by login if it is possible.
     */
    public function check(string $password): bool {
        $result = false;
        
        if ($this->login && $password) {
            if ($this->_password === $password) {
                $result = true;
            }
        }
        
        return $result;
    }
    
    /**
     * Password can't be gotten.
     */
    public function getPassword() {
        return null;
    }
    
}
