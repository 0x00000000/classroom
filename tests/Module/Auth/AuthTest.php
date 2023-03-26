<?php

declare(strict_types=1);

namespace ClassroomTest\Module\Auth;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

include_once(dirname(__FILE__) . '/../../init.php');

final class AuthTest extends TestCase {

    private $_request = null;

    private $_auth = null;

    private $_existingUserModel = null;

    private $_existingPassword = null;

    public function setUp(): void {
        $this->_request = Factory::instance()->createRequest();

        $this->_auth = Factory::instance()->createAuth($this->_request);

        $this->_existingPassword = $this->getUniquePassword();
        $this->_existingUserModel = $this->createUser(
            $this->getUniqueLogin(),
            $this->_existingPassword
        );

        // I want be sure that we are not logged in.
        $this->_auth->logout();
    }

    public function testLogin(): void {
        $result = $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );
        $this->assertTrue($result);

        $result = $this->_auth->login(
            $this->_existingUserModel->login,
            $this->getUniquePassword()
        );
        $this->assertFalse($result);
    }

    public function testLogout(): void {
        $result = $this->_auth->logout();
        $this->assertFalse($result);

        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );
        $result = $this->_auth->logout();
        $this->assertTrue($result);

        $result = $this->_auth->logout();
        $this->assertFalse($result);
    }

    public function testGetUser(): void {
        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );

        $userModel = $this->_auth->getUser();
        $this->assertTrue($userModel instanceof  \Classroom\Model\Model);
        $this->assertEquals($userModel->login, $this->_existingUserModel->login);

        $this->_auth->logout();
        $userModel = $this->_auth->getUser();
        $this->assertNull($userModel);
    }

    public function testCheck(): void {
        $login = $this->getUniqueLogin();
        $password = $this->getUniquePassword();
        $userModel = $this->createUser($login, $password);
        $result = $this->_auth->check(
            $login,
            $password
        );
        $this->assertTrue($result);

        $result = $this->_auth->check(
            $this->getUniqueLogin(),
            $password
        );
        $this->assertFalse($result);

        $result = $this->_auth->check(
            $login,
            $this->getUniquePassword()
        );
        $this->assertFalse($result);
    }

    public function testIsInactive(): void {
        $login = $this->getUniqueLogin();
        $password = $this->getUniquePassword();
        $userModel = $this->createUser($login, $password);

        $userModel->disabled = false;
        $userModel->save();
        $result = $this->_auth->isInactive(
            $login,
            $password
        );
        $this->assertFalse($result);

        $userModel->disabled = true;
        $userModel->save();
        $result = $this->_auth->isInactive(
            $login,
            $password
        );
        $this->assertTrue($result);

        $result = $this->_auth->isInactive(
            $this->getUniqueLogin(),
            $password
        );
        $this->assertFalse($result);

        $result = $this->_auth->isInactive(
            $login,
            $this->getUniquePassword()
        );
        $this->assertFalse($result);
    }

    public function testIsAdmin(): void {
        $this->_existingUserModel->isAdmin = true;
        $this->_existingUserModel->save();
        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );
        $result = $this->_auth->isAdmin();
        $this->assertTrue($result);

        $this->_existingUserModel->isAdmin = false;
        $this->_existingUserModel->save();
        $this->_auth->logout();
        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );
        $result = $this->_auth->isAdmin();
        $this->assertFalse($result);
    }

    public function testIsTeacher(): void {
        $this->_existingUserModel->isTeacher = true;
        $this->_existingUserModel->save();
        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );
        $result = $this->_auth->isTeacher();
        $this->assertTrue($result);

        $this->_existingUserModel->isTeacher = false;
        $this->_existingUserModel->save();
        $this->_auth->logout();
        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );
        $result = $this->_auth->isTeacher();
        $this->assertFalse($result);
    }

    public function testIsStudent(): void {
        $this->_existingUserModel->isStudent = true;
        $this->_existingUserModel->save();
        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );
        $result = $this->_auth->isStudent();
        $this->assertTrue($result);

        $this->_existingUserModel->isStudent = false;
        $this->_existingUserModel->save();
        $this->_auth->logout();
        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );
        $result = $this->_auth->isStudent();
        $this->assertFalse($result);
    }

    public function testIsGuest(): void {
        $result = $this->_auth->isGuest();
        $this->assertTrue($result);

        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->getUniquePassword()
        );
        $result = $this->_auth->isGuest();
        $this->assertTrue($result);

        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );
        $result = $this->_auth->isGuest();
        $this->assertFalse($result);

        $this->_auth->logout();
        $result = $this->_auth->isGuest();
        $this->assertTrue($result);
    }

    public function testSetRequest(): void {
        $result = $this->_auth->setRequest($this->_request);
        $this->assertTrue($result);
    }

    private function getUniqueLogin(): string {
        static $loginCounter = 0;
        $loginCounter++;

        return __CLASS__ . '_login_' . $loginCounter;
    }

    private function getUniquePassword(): string {
        static $passwordCounter = 0;
        $passwordCounter++;

        return __CLASS__ . '_password_' . $passwordCounter;
    }

    private function createUser($login, $password, $name = 'name') {
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
