<?php

declare(strict_types=1);

namespace ClassroomTest;

use Classroom\Module\Factory\Factory;

trait UsesModelUserTrait {
    protected $_existingUserModel = null;

    protected $_existingPassword = null;

    protected function getUniqueLogin(): string {
        static $loginCounter = 0;
        $loginCounter++;

        return __CLASS__ . '_login_' . $loginCounter;
    }

    protected function getUniquePassword(): string {
        static $passwordCounter = 0;
        $passwordCounter++;

        return __CLASS__ . '_password_' . $passwordCounter;
    }

    protected function createUser($login, $password, $name = 'name') {
        $modelUser = Factory::instance()->createModel('User');
        $modelUser->login = $login;
        $modelUser->name = $name;
        $modelUser->password = $password;
        $modelUser->disabled = false;
        $modelUser->deleted = false;
        $modelUser->save();

        return $modelUser;
    }
}