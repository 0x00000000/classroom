<?php

declare(strict_types=1);

namespace classroom;

include_once('Auth.php');

/**
 * Allows to get authorized user information.
 */
abstract class AuthBase extends Auth {
    
    /**
     * @var User|null $_user Current user object.
     */
    protected $_user = null;
    
    /**
     * @var Request|null $_request Request object.
     */
    protected $_request = null;
    
    /**
     * Gets current user.
     */
    public function __construct() {
    }
    
    /**
     * Gets current user.
     */
    public function getUser(): ?ModelUser {
        if (! $this->_user) {
            $this->_user = $this->getUserFromSession();
        }
        
        return $this->_user;
    }
    
    /**
     * Check user by user auth information.
     */
    public function check($login, $password): bool {
        $result = false;
        
        if (! $this->getUser()) {
            $user = Factory::instance()->createModel('User');
            $result = $user->check($login, $password);
        }
        
        return $result;
    }
    
    /**
     * Login user by user auth information.
     */
    public function login(string $login, string $password): bool {
        $result = false;
        
        if (! $this->getUser()) {
            if ($this->check($login, $password)) {
                $user = Factory::instance()->createModel('User');
                if ($user->loadByLogin($login, $password)) {
                    $this->_user = $user;
                    $this->setUserToSession($user);
                    $result = true;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Logout current user by user auth information.
     */
    public function logout(): bool {
        $result = false;
        
        if ($this->getUser()) {
            $this->_user = null;
            $this->setUserToSession($this->_user);
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Check if user is admin.
     */
    public function isAdmin() {
        $result = false;
        
        if ($this->getUser()) {
            $result = $this->getUser()->isAdmin;
        }
        
        return $result;
    }
    
    /**
     * Check if user is teacher.
     */
    public function isTeacher() {
        $result = false;
        
        if ($this->getUser()) {
            $result = $this->getUser()->isTeacher;
        }
        
        return $result;
    }
    
    /**
     * Check if user is student.
     */
    public function isStudent() {
        $result = false;
        
        if ($this->getUser()) {
            $result = $this->getUser()->isStudent;
        }
        
        return $result;
    }
    
    /**
     * Check if user is guest.
     */
    public function isGuest() {
        $result = false;
        
        if (! $this->getUser()) {
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Gets request object.
     */
    protected function getRequest(): Request {
        return $this->_request;
    }
    
    /**
     * Sets request object.
     */
    public function setRequest(Request $request): bool {
        $result = false;
        
        if (is_object($request) && $request instanceof Request) {
            $this->_request = $request;
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Gets user from session.
     */
    protected function getUserFromSession(): ?ModelUser {
        $user = null;
        
        if ($this->getRequest()) {
            $session = $this->getRequest()->session;
            if (is_array($session) && array_key_exists('userId', $session)) {
                $userId = $session['userId'];
                if ($userId) {
                    $user = Factory::instance()->createModel('User');
                    $success = $user->loadById($userId);
                    if (! $success) {
                        $user = null;
                    }
                }
            }
        }
        
        return $user;
    }
    
    /**
     * Sets user to session.
     */
    protected function setUserToSession(?ModelUser $user): bool {
        $result = false;
        
        if ($this->getRequest()) {
            if ($user && $user->id) {
                $userId = $user->id;
            } else {
                $userId = null;
            }
            $this->getRequest()->setSessionVariable('userId', $userId);
        }
        
        return $result;
    }
    
}
