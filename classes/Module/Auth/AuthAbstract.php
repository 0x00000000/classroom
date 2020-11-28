<?php

declare(strict_types=1);

namespace Classroom\Module\Auth;

use Classroom\Module\Request\Request;

use Classroom\Model\ModelUser;

/**
 * Allows to get authorized user information.
 */
abstract class AuthAbstract {
    
    /**
     * Gets current user.
     */
    abstract public function getUser(): ?ModelUser;
    
    /**
     * Check user by user auth information.
     */
    abstract public function check($login, $password): bool;
    
    /**
     * Login user by user auth information.
     */
    abstract public function login(string $login, string $password): bool;
    
    /**
     * Logout current user by user auth information.
     */
    abstract public function logout(): bool;
    
    /**
     * Check if user is admin.
     */
    abstract public function isAdmin();
    
    /**
     * Check if user is teacher.
     */
    abstract public function isTeacher();
    
    /**
     * Check if user is student.
     */
    abstract public function isStudent();
    
    /**
     * Check if user is guest.
     */
    abstract public function isGuest();
    
    /**
     * Gets request object.
     */
    abstract protected function getRequest(): Request;
    
    /**
     * Sets request object.
     */
    abstract public function setRequest(Request $request): bool;
    
}