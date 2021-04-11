<?php

declare(strict_types=1);

namespace Classroom\Model;

use Classroom\Module\Config\Config;
use Classroom\Module\Factory\Factory;

/**
 * Model user.
 * 
 * @property string|null $id User's id.
 * @property string|null $teacherId Teacher's id. Hidden.
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
    
    /**
     * User's access levels.
     */
    public const ACCESS_ADMIN = 1;
    public const ACCESS_TEACHER = 2;
    public const ACCESS_STUDENT = 4;
    public const ACCESS_GUEST = 8;
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'user';
    
    /**
     * @var array $_propertiesList List of properties.
     */
    protected $_propertiesList = array(
        array('name' => 'id'),
        array('name' => 'teacherId', 'skipControl' => true),
        array('name' => 'login'),
        array('name' => 'password'),
        array('name' => 'name'),
        array('name' => 'isAdmin', 'type' => self::TYPE_BOOL),
        array('name' => 'isTeacher', 'type' => self::TYPE_BOOL),
        array('name' => 'isStudent', 'type' => self::TYPE_BOOL),
        array('name' => 'disabled', 'type' => self::TYPE_BOOL),
        array('name' => 'deleted', 'type' => self::TYPE_BOOL, 'skipControl' => true),
    );
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
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
     * Checks existing inactive user with login and password.
     */
    public function checkInactive($login, $password) {
        $result = false;
        
        $dbData = $this->getDataRecord(
            array(
                'login' => $login,
                'password' => $this->encodePassword($password),
                'disabled' => '1',
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
    public function setPassword($value) {
        $password = $this->encodePassword($value);
        $this->setRawProperty('password', $password);
    }
    
    /**
     * Teacher's id can't be gotten.
     */
    public function getTeacherId() {
        return null;
    }
    
    /**
     * Teacher's id can't be set.
     */
    public function setTeacherId($value) {
    }
    
    /**
     * Encodes password.
     */
    protected function encodePassword($password) {
        $salt1 = Config::instance()->get('user', 'salt1');
        $salt2 = Config::instance()->get('user', 'salt2');
        return hash('sha512', $salt1 . $password . $salt2);
    }
    
    /**
     * Gets students list.
     */
    public function getStudentsList(
        ?int $limit = 0, ?int $offset = 0,
        ?array $sortingList = array('id' => 'desc'),
        ?array $extraConditionsList = array()
    ): array {
        $list = array();
        if ($this->getPk() && $this->isTeacher) {
            $conditionsList = array('teacherId' => $this->getPk(), 'isStudent' => true, 'deleted' => false);
            if ($extraConditionsList) {
                $conditionsList = array_merge($conditionsList, $extraConditionsList);
            }
            $list = $this->getModelsList(
                $conditionsList,
                $limit,
                $offset,
                $sortingList
            );
        }
        
        return $list;
    }
    
    /**
     * Gets students count for teacher.
     */
    public function getStudentsCount(?array $extraConditionsList = array()): int {
        $count = 0;
        if ($this->getPk() && $this->isTeacher) {
            $conditionsList = array('teacherId' => $this->getPk(), 'isStudent' => true, 'deleted' => false);
            if ($extraConditionsList) {
                $conditionsList = array_merge($conditionsList, $extraConditionsList);
            }
            $count = $this->getCount($conditionsList);
        }
        
        return $count;
    }
    
    /**
     * Gets teachers list.
     */
    public function getTeachersList(
        ?int $limit = 0, ?int $offset = 0,
        ?array $sortingList = array('id' => 'desc')
    ): array {
        $list = array();
        if ($this->getPk() && $this->isStudent) {
            if ($this->getRawProperty('teacherId')) {
                $modelName = $this->getModelName();
                $teacher = Factory::instance()->createModel($modelName);
                if ($teacher->loadByPk($this->getRawProperty('teacherId'))) {
                    if (! $teacher->deleted) {
                        $list = array($teacher);
                    }
                }
            }
        }
        
        return $list;
    }
    
    /**
     * Gets teachers count for student.
     */
    public function getTeachersCount(): int {
        $count = 1;
        
        return $count;
    }
    
    /**
     * Sets teacher for student.
     */
    public function setTeacher(ModelUser $teacher): bool {
        $result = false;
        
        if ($this->isStudent) {
            if ($teacher->getPk() && $teacher->isTeacher) {
                if ($this->setRawProperty('teacherId', $teacher->getPk())) {
                    $result = true;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Gets access levels values. May be used in controls.
     */
    public static function getAccessValues(): array {
        return array(
            self::ACCESS_ADMIN => 'admin',
            self::ACCESS_TEACHER => 'teacher',
            self::ACCESS_STUDENT => 'student',
            self::ACCESS_GUEST => 'guest',
        );
    }
    
}
