<?php

declare(strict_types=1);

namespace Classroom\Model;

use Classroom\Model\ModelDatabase;
use Classroom\Module\Auth\Auth;

/**
 * Model with user access property.
 * 
 * @property bool $accessAdmin Access rights for viewing.
 * @property bool $accessTeacher Access rights for viewing.
 * @property bool $accessStudent Access rights for viewing.
 * @property bool $accessGuest Access rights for viewing.
 */
abstract class ModelAccessRights extends ModelDatabase {
    
    public function userHasAccess(Auth $auth): bool {
        $result = (
            ($auth->isAdmin() && $this->accessAdmin)
            || ($auth->isTeacher() && $this->accessTeacher)
            || ($auth->isStudent() && $this->accessStudent)
            || ($auth->isGuest() && $this->accessGuest)
        );
        
        return $result;
    }
    
}
