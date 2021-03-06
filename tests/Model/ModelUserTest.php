<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

use Classroom\Model\ModelUser;

include_once(dirname(__FILE__) . '/../init.php');

Factory::instance()->loadModule('ModelUser');

final class ModelUserTest extends TestCase {

    public function testPassword(): void {
        $uid = rand();
        $testLogin = 'testLogin' . $uid;
        $testName = 'Test Name';
        $testPassword = 'Test password';
        $modelUser = Factory::instance()->createModel('User');
        $modelUser->login = $testLogin;
        $modelUser->name = $testName;

        $this->assertTrue($modelUser->password === null);

        $modelUser->password = $testPassword;

        $this->assertTrue($modelUser->password === null);
    }

    public function testLoadByLogin(): void {
        $uid = rand();
        $testLogin = 'testLogin' . $uid;
        $testLoginUnique = 'testLoginUnique' . $uid;
        $testName = 'Test Name';
        $testPassword = 'Test password';

        $modelUser = Factory::instance()->createModel('User');
        $modelUser->login = $testLogin;
        $modelUser->name = $testName;
        $modelUser->password = $testPassword;
        $modelUser->disabled = false;
        $modelUser->deleted = false;
        $modelUser->save();

        $modelUser = Factory::instance()->createModel('User');
        $modelUser->login = $testLoginUnique;
        $modelUser->name = $testName;
        $modelUser->password = $testPassword;
        $modelUser->isAdmin = true;
        $modelUser->isTeacher = true;
        $modelUser->isStudent = true;
        $modelUser->disabled = false;
        $modelUser->deleted = false;

        $this->assertFalse($modelUser->loadByLogin($testLoginUnique, $testPassword));

        $modelUser->save();

        $modelUserLoaded = Factory::instance()->createModel('User');
        $this->assertFalse($modelUserLoaded->loadByLogin($testLoginUnique, 'Another password'));
        $this->assertFalse($modelUserLoaded->loadByLogin($testLoginUnique, ''));
        $this->assertTrue($modelUserLoaded->loadByLogin($testLoginUnique, $testPassword));
        $this->assertEquals($modelUserLoaded->login, $testLoginUnique);
        $this->assertEquals($modelUserLoaded->name, $testName);
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
        $this->assertFalse($modelUserLoaded2->loadByLogin($testLoginUnique, $testPassword));

        $modelUserLoaded->disabled = false;
        $modelUserLoaded->deleted = true;
        $modelUserLoaded->save();
        $this->assertFalse($modelUserLoaded2->loadByLogin($testLoginUnique, $testPassword));

        $modelUserLoaded->disabled = false;
        $modelUserLoaded->deleted = false;
        $modelUserLoaded->save();
        $this->assertTrue($modelUserLoaded2->loadByLogin($testLoginUnique, $testPassword));

        $this->assertEquals($modelUserLoaded2->login, $testLoginUnique);
        $this->assertEquals($modelUserLoaded2->name, $testName);
        $this->assertEquals($modelUserLoaded2->isAdmin, false);
        $this->assertEquals($modelUserLoaded2->isTeacher, false);
        $this->assertEquals($modelUserLoaded2->isStudent, false);
    }

    public function testCheck(): void {
        $uid = rand();
        $testLogin = 'testLogin' . $uid;

        $testName = 'Test Name';
        $testPassword = 'Test password';
        $modelUser = Factory::instance()->createModel('User');
        $modelUser->login = $testLogin;
        $modelUser->name = $testName;
        $modelUser->password = $testPassword;
        $modelUser->disabled = false;
        $modelUser->deleted = false;
        $modelUser->save();
        $this->assertTrue($modelUser->check($testLogin, $testPassword));
        $this->assertFalse($modelUser->check('Another login', $testPassword));
        $this->assertFalse($modelUser->check($testLogin, 'Another password'));
        $this->assertFalse($modelUser->check($testLogin, ''));

        $modelUser->login = $testLogin;
        $modelUser->name = $testName;
        $modelUser->password = 'Another password';
        $modelUser->save();
        $this->assertFalse($modelUser->check($testLogin, $testPassword));
        $modelUser->password = $testPassword;
        $modelUser->save();
        $this->assertTrue($modelUser->check($testLogin, $testPassword));

        $modelUser->login = $testLogin;
        $modelUser->name = $testName;
        $modelUser->password = '';
        $modelUser->save();
        $this->assertFalse($modelUser->check($testLogin, $testPassword));
        $modelUser->password = $testPassword;
        $modelUser->save();
        $this->assertTrue($modelUser->check($testLogin, $testPassword));
    }

    public function testSetRole(): void {
        $uid = rand();
        $testLogin = 'testLogin' . $uid;

        $modelUser = Factory::instance()->createModel('User');
        $modelUser->login = $testLogin;
        $modelUser->name = 'Test Name';

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
        $uid = rand();
        $testLogin = 'testLogin' . $uid;

        $modelUserSave = Factory::instance()->createModel('User');
        $modelUserSave->login = $testLogin;
        $modelUserSave->name = 'Test Name';
        $idSave = $modelUserSave->save();
        $this->assertTrue(boolval($idSave));
        $dataAfterSave = $modelUserSave->getDataAssoc();

        $uid = rand();
        $testLogin = 'testLogin' . $uid;

        $uid = rand();
        $testLogin = 'testLogin' . $uid;

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

        $uid = rand();
        $testLogin = 'testLogin' . $uid;

        $modelUserUpdatedGet = Factory::instance()->createModel('User');
        $modelUserSave->login = $testLogin;
        $modelUserSave->name = 'Test Name 3';
        $modelUserUpdatedGet->loadByPk($idGet);

        $dataAfterUpdatedGet = $modelUserUpdatedGet->getDataAssoc();

        $this->assertEquals($dataAfterUpdated, $dataAfterUpdatedGet);
    }

    public function testGetOneModel(): void {

        $uid = rand();
        $testLogin = 'testLogin' . $uid;
        $testLoginUnique = 'testLoginUnique' . $uid;
        $testName = 'Test Name' . $uid;
        $testPassword = 'Test password';

        $modelUser = Factory::instance()->createModel('User');
        $modelUser->login = $testLogin;
        $modelUser->name = $testName;
        $modelUser->password = $testPassword;
        $modelUser->isAdmin = true;
        $modelUser->isTeacher = false;
        $modelUser->isStudent = false;
        $modelUser->disabled = false;
        $modelUser->deleted = false;
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

}
