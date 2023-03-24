<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

use Classroom\Model\ModelUser;

include_once(dirname(__FILE__) . '/../init.php');

Factory::instance()->loadModule('ModelUser');

final class ModelUserTest extends TestCase {

    public function testLoadByLogin(): void {
        $login = $this->getUniqueLogin();
        $password = 'password';
        $name = 'name';
        $this->createUser($this->getUniqueLogin(), $password);

        $modelUser = Factory::instance()->createModel('User');
        $modelUser->login = $login;
        $modelUser->name = $name;
        $modelUser->password = $password;
        $modelUser->isAdmin = true;
        $modelUser->isTeacher = true;
        $modelUser->isStudent = true;
        $modelUser->disabled = false;
        $modelUser->deleted = false;

        $this->assertFalse($modelUser->loadByLogin($login, $password));
        $modelUser->save();

        $modelUserLoaded = Factory::instance()->createModel('User');
        $this->assertFalse($modelUserLoaded->loadByLogin($login, 'Another password'));
        $this->assertFalse($modelUserLoaded->loadByLogin($login, ''));
        $this->assertTrue($modelUserLoaded->loadByLogin($login, $password));
        $this->assertEquals($modelUserLoaded->login, $login);
        $this->assertEquals($modelUserLoaded->name, $name);
        $this->assertEquals($modelUserLoaded->isAdmin, true);
        $this->assertEquals($modelUserLoaded->isTeacher, true);
        $this->assertEquals($modelUserLoaded->isStudent, true);

        $modelUserLoaded->isAdmin = false;
        $modelUserLoaded->isTeacher = false;
        $modelUserLoaded->isStudent = false;
        $modelUserLoaded->disabled = true;
        $modelUserLoaded->deleted = false;
        $modelUserLoaded->save();

        $modelUserLoaded2 = Factory::instance()->createModel('User');
        $this->assertFalse($modelUserLoaded2->loadByLogin($login, $password));

        $modelUserLoaded->disabled = false;
        $modelUserLoaded->deleted = true;
        $modelUserLoaded->save();
        $this->assertFalse($modelUserLoaded2->loadByLogin($login, $password));

        $modelUserLoaded->disabled = false;
        $modelUserLoaded->deleted = false;
        $modelUserLoaded->save();
        $this->assertTrue($modelUserLoaded2->loadByLogin($login, $password));

        $this->assertEquals($modelUserLoaded2->login, $login);
        $this->assertEquals($modelUserLoaded2->name, $name);
        $this->assertEquals($modelUserLoaded2->isAdmin, false);
        $this->assertEquals($modelUserLoaded2->isTeacher, false);
        $this->assertEquals($modelUserLoaded2->isStudent, false);
    }

    public function testCheck(): void {
        $testLogin = $this->getUniqueLogin();
        $testPassword = $this->getUniquePassword();
        $modelUser = $this->createUser($testLogin, $testPassword);

        $modelUser->save();
        $this->assertTrue($modelUser->check($testLogin, $testPassword));
        $this->assertFalse($modelUser->check('Another login', $testPassword));
        $this->assertFalse($modelUser->check($testLogin, 'Another password'));
        $this->assertFalse($modelUser->check($testLogin, ''));

        $modelUser->disabled = true;
        $modelUser->deleted = false;
        $modelUser->save();
        $this->assertFalse($modelUser->check($testLogin, $testPassword));

        $modelUser->disabled = false;
        $modelUser->deleted = true;
        $modelUser->save();
        $this->assertFalse($modelUser->check($testLogin, $testPassword));

        $modelUser->disabled = true;
        $modelUser->deleted = true;
        $modelUser->save();
        $this->assertFalse($modelUser->check($testLogin, $testPassword));
    }

    public function testCheckInactive(): void {
        $testLogin = $this->getUniqueLogin();
        $testPassword = $this->getUniquePassword();
        $modelUser = $this->createUser($testLogin, $testPassword);
        $modelUser->disabled = true;
        $modelUser->save();

        $this->assertTrue($modelUser->checkInactive($testLogin, $testPassword));
        $this->assertFalse($modelUser->checkInactive('Another login', $testPassword));
        $this->assertFalse($modelUser->checkInactive($testLogin, 'Another password'));
        $this->assertFalse($modelUser->checkInactive($testLogin, ''));

        $modelUser->disabled = false;
        $modelUser->deleted = false;
        $modelUser->save();
        $this->assertFalse($modelUser->checkInactive($testLogin, $testPassword));

        $modelUser->disabled = true;
        $modelUser->deleted = true;
        $modelUser->save();
        $this->assertFalse($modelUser->checkInactive($testLogin, $testPassword));

        $modelUser->disabled = false;
        $modelUser->deleted = false;
        $modelUser->save();
        $this->assertFalse($modelUser->checkInactive($testLogin, $testPassword));
    }

    public function testGetPassword(): void {
        $testLogin = $this->getUniqueLogin();
        $testPassword = $this->getUniquePassword();
        $modelUser = $this->createUser($testLogin, $testPassword);

        $this->assertEquals($modelUser->password, null);

        $modelUser->password = $testPassword;
        $this->assertEquals($modelUser->password, null);
        $modelUser->save();
        $this->assertEquals($modelUser->password, null);
    }

    public function testSetPassword(): void {
        $testLogin = $this->getUniqueLogin();
        $testPassword = $this->getUniquePassword();
        $modelUser = $this->createUser($testLogin, $testPassword);

        $modelUser->password = 'Another password';
        $modelUser->save();
        $this->assertFalse($modelUser->check($testLogin, $testPassword));
        $modelUser->password = $testPassword;
        $modelUser->save();
        $this->assertTrue($modelUser->check($testLogin, $testPassword));

        $modelUser->password = '';
        $modelUser->save();
        $this->assertFalse($modelUser->check($testLogin, $testPassword));
        $modelUser->password = $testPassword;
        $modelUser->save();
        $this->assertTrue($modelUser->check($testLogin, $testPassword));
    }

    public function testGetTeacherId(): void {
        $testLogin = $this->getUniqueLogin();
        $testPassword = $this->getUniquePassword();
        $modelUser = $this->createUser($testLogin, $testPassword);
        $modelUser->isStudent = true;
        $modelUser->save();

        $this->assertEquals($modelUser->teacherId, null);
    }

    public function testSetRole(): void {
        $testLogin = $this->getUniqueLogin();
        $testPassword = $this->getUniquePassword();
        $modelUser = $this->createUser($testLogin, $testPassword);

        $modelUser->isAdmin = true;
        $this->assertEquals($modelUser->isAdmin, true);

        $modelUser->isTeacher = true;
        $this->assertEquals($modelUser->isTeacher, true);

        $modelUser->isStudent = true;
        $this->assertEquals($modelUser->isStudent, true);

        $modelUser->isAdmin = false;
        $this->assertEquals($modelUser->isAdmin, false);

        $modelUser->isTeacher = false;
        $this->assertEquals($modelUser->isTeacher, false);

        $modelUser->isStudent = false;
        $this->assertEquals($modelUser->isStudent, false);

        $modelUser->isAdmin = 1;
        $this->assertEquals($modelUser->isAdmin, true);

        $modelUser->isTeacher = 1;
        $this->assertEquals($modelUser->isTeacher, true);

        $modelUser->isStudent = 1;
        $this->assertEquals($modelUser->isStudent, true);

        $modelUser->isAdmin = 0;
        $this->assertEquals($modelUser->isAdmin, false);

        $modelUser->isTeacher = 0;
        $this->assertEquals($modelUser->isTeacher, false);

        $modelUser->isStudent = 0;
        $this->assertEquals($modelUser->isStudent, false);
    }

    public function testDatabase(): void {
        $testLogin = $this->getUniqueLogin();
        $modelUserSave = Factory::instance()->createModel('User');
        $modelUserSave->login = $testLogin;
        $modelUserSave->name = 'Test Name';
        $idSave = $modelUserSave->save();
        $this->assertTrue(boolval($idSave));
        $dataAfterSave = $modelUserSave->getDataAssoc();

        $testLogin = $this->getUniqueLogin();

        $modelUserGet = Factory::instance()->createModel('User');
        $modelUserSave->login = $testLogin;
        $modelUserSave->name = 'Test Name 2';
        $modelUserGet->loadByPk($idSave);
        $dataAfterGet = $modelUserGet->getDataAssoc();

        $this->assertEquals($dataAfterSave, $dataAfterGet);

        $modelUserGet->login .= '3';
        $modelUserGet->name .= '!';
        $idGet = $modelUserGet->save();
        $dataAfterUpdated = $modelUserGet->getDataAssoc();

        $testLogin = $this->getUniqueLogin();

        $modelUserUpdatedGet = Factory::instance()->createModel('User');
        $modelUserSave->login = $testLogin;
        $modelUserSave->name = 'Test Name 3';
        $modelUserUpdatedGet->loadByPk($idGet);

        $dataAfterUpdatedGet = $modelUserUpdatedGet->getDataAssoc();

        $this->assertEquals($dataAfterUpdated, $dataAfterUpdatedGet);
    }

    public function testGetOneModel(): void {
        $testLogin = $this->getUniqueLogin();
        $testPassword = $this->getUniquePassword();
        $testName = 'name';
        $modelUser = $this->createUser($testLogin, $testPassword, $testName);
        $modelUser->isAdmin = true;
        $modelUser->isTeacher = false;
        $modelUser->isStudent = false;
        $modelUser->save();

        $modelAnotherUser = Factory::instance()->createModel('User');

        $modelUser2 = $modelAnotherUser->getOneModel(array(
            'login' => $testLogin,
            'isAdmin' => true,
            'isTeacher' => true,
            'isStudent' => false,
        ));
        $this->assertNull($modelUser2);

        $modelUser2 = $modelAnotherUser->getOneModel(array(
            'login' => $testLogin,
            'isAdmin' => true,
            'isTeacher' => false,
            'isStudent' => false,
        ));
        $this->assertTrue($modelUser2 instanceof \Classroom\Model\Model);

        if ($modelUser2 instanceof \Classroom\Model\Model) {
            $this->assertEquals($modelUser2->name, $testName);
        }
    }

    public function testGetStudentsList(): void {
        $users = $this->createTeacherAndStudents();
        $teacher = $users['teacher'];
        $studentsList = $users['studentsList'];

        $gotList = $teacher->getStudentsList();
        $this->assertEquals(count($gotList), count($studentsList));
    }

    public function testGetStudentsCount(): void {
        $users = $this->createTeacherAndStudents();
        $teacher = $users['teacher'];
        $studentsList = $users['studentsList'];

        $count = $teacher->getStudentsCount();
        $this->assertEquals($count, count($studentsList));
    }

    public function testGetTeachersList(): void {
        $users = $this->createTeacherAndStudents();
        $teacher = $users['teacher'];
        $studentsList = $users['studentsList'];

        $gotList = $studentsList[0]->getTeachersList();
        $this->assertEquals(count($gotList), 1);
        $this->assertEquals($gotList[0]->login, $teacher->login);
    }

    public function testGetTeachersCount(): void {
        $users = $this->createTeacherAndStudents();
        $teacher = $users['teacher'];
        $studentsList = $users['studentsList'];

        $count = $teacher->getTeachersCount();
        $this->assertEquals($count, 1);
    }

    private function getUniqueLogin(): string {
        static $loginCounter = 0;
        $loginCounter++;
        return 'login' . $loginCounter;
    }

    private function getUniquePassword(): string {
        static $passwordCounter = 0;
        $passwordCounter++;
        return 'password' . $passwordCounter;
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

    private function createTeacherAndStudents(): array {
        $teacher = $this->createUser($this->getUniqueLogin(), $this->getUniquePassword());
        $teacher->isTeacher = true;
        $teacher->save();

        $studentsList = [];
        $modelUser = $this->createUser($this->getUniqueLogin(), $this->getUniquePassword());
        $modelUser->isStudent = true;
        $modelUser->setTeacher($teacher);
        $modelUser->save();
        $studentsList[] = $modelUser;

        $modelUser = $this->createUser($this->getUniqueLogin(), $this->getUniquePassword());
        $modelUser->isStudent = true;
        $modelUser->setTeacher($teacher);
        $modelUser->save();
        $studentsList[] = $modelUser;

        $modelUser = $this->createUser($this->getUniqueLogin(), $this->getUniquePassword());
        $modelUser->isStudent = true;
        $modelUser->setTeacher($teacher);
        $modelUser->save();
        $studentsList[] = $modelUser;

        return ['teacher' => $teacher, 'studentsList' => $studentsList];
    }
}
