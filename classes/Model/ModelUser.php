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
 * @property bool $disabled Is user disabled.
 * @property bool $deleted Is user deleted.
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
    protected $_isAdmin = '0';
    protected $_isTeacher = '0';
    protected $_isStudent = '0';
    protected $_disabled = '0';
    protected $_deleted = '0';
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'user';
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
        
        $this->addBoolProperty('_isAdmin');
        $this->addBoolProperty('_isTeacher');
        $this->addBoolProperty('_isStudent');
        $this->addBoolProperty('_disabled');
        $this->addBoolProperty('_deleted');
    }
    
    /**
     * Loads user from database by login if it is possible.
     */
    public function loadByLogin(string $login, string $password): bool {
        $result = false;
        
        $dbData = $this->getDataRecord(
            array(
                'login' => $login,
                'password' => $this->encodePassword($password),
                'disabled' => '0',
                'deleted' => '0'
            )
        );
        if ($dbData && count($dbData)) {
            $result = $this->setDataFromDB($dbData);
        }
        
        return $result;
    }
    
    /**
     * Checks existing user with login and password.
     */
    public function check(string $login, string $password): bool {
        $result = false;
        
        $dbData = $this->getDataRecord(
            array(
                'login' => $login,
                'password' => $this->encodePassword($password),
                'disabled' => '0',
                'deleted' => '0'
            )
        );
        if ($dbData && count($dbData)) {
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Password can't be gotten.
     */
    public function getPassword() {
        return null;
    }
    
    /**
     * Sets hash instead of password.
     */
    public function setPassword($password) {
        $this->_password = $this->encodePassword($password);
    }
    
    /**
     * Encodes password.
     */
    protected function encodePassword($password) {
        $salt1 = Config::instance()->get('user', 'salt1');;
        $salt2 = Config::instance()->get('user', 'salt2');;
        return hash('sha512', $salt1 . $password . $salt2);
    }
    
}
