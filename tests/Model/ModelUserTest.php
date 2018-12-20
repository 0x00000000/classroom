<?php

declare(strict_types=1);

namespace classroom;

use PHPUnit\Framework\TestCase;

include_once(dirname(__FILE__) . '/../init.php');

Factory::instance()->loadModule('ModelUser');

final class ModelUserTest extends TestCase {
    
    public function testPassword(): void {
        $testLogin = 'testLogin';
        $testName = 'Test Name';
        $testPassword = 'Test password';
        $modelUser = Factory::instance()->createModelUser();
        $modelUser->login = $testLogin;
        $modelUser->name = $testName;
        
        $this->assertTrue($modelUser->password === null);
        
        $modelUser->password = $testPassword;
        
        $this->assertTrue($modelUser->password === null);
    }
    
    public function testLoadByLogin(): void {
        $testLogin = 'testLogin';
        $testLoginUnique = 'testLogin unique';
        $testName = 'Test Name';
        
        $modelUser = Factory::instance()->createModelUser();
        $modelUser->login = $testLogin;
        $modelUser->name = $testName;
        $modelUser->save();
        
        $modelUser = Factory::instance()->createModelUser();
        $modelUser->login = $testLoginUnique;
        $modelUser->name = $testName;
        $modelUser->isAdmin = true;
        $modelUser->isTeacher = true;
        $modelUser->isStudent = true;
        
        $this->assertFalse($modelUser->loadByLogin($testLoginUnique));
        
        $modelUser->save();
        
        $modelUserLoaded = Factory::instance()->createModelUser();
        $this->assertTrue($modelUserLoaded->loadByLogin($testLoginUnique));
        $this->assertEquals($modelUserLoaded->login, $testLoginUnique);
        $this->assertEquals($modelUserLoaded->name, $testName);
        $this->assertEquals($modelUserLoaded->isAdmin, true);
        $this->assertEquals($modelUserLoaded->isTeacher, true);
        $this->assertEquals($modelUserLoaded->isStudent, true);
        
        $modelUserLoaded->isAdmin = false;
        $modelUserLoaded->isTeacher = false;
        $modelUserLoaded->isStudent = false;
        $modelUserLoaded->save();
        
        $modelUserLoaded2 = Factory::instance()->createModelUser();
        $this->assertTrue($modelUserLoaded2->loadByLogin($testLoginUnique));
        $this->assertEquals($modelUserLoaded2->login, $testLoginUnique);
        $this->assertEquals($modelUserLoaded2->name, $testName);
        $this->assertEquals($modelUserLoaded2->isAdmin, false);
        $this->assertEquals($modelUserLoaded2->isTeacher, false);
        $this->assertEquals($modelUserLoaded2->isStudent, false);
        
    }
    
    public function testCheck(): void {
        $testLogin = 'testLogin';
        $testName = 'Test Name';
        $testPassword = 'Test password';
        $modelUser = Factory::instance()->createModelUser();
        $modelUser->login = $testLogin;
        $modelUser->name = $testName;
        $modelUser->password = $testPassword;
        $this->assertTrue($modelUser->check($testPassword));
        $this->assertFalse($modelUser->check('Another password'));
        $this->assertFalse($modelUser->check(''));
        
        $modelUser = Factory::instance()->createModelUser();
        $modelUser->login = $testLogin;
        $modelUser->name = $testName;
        $modelUser->password = 'Another password';
        $this->assertFalse($modelUser->check($testPassword));
        $modelUser->password = $testPassword;
        $this->assertTrue($modelUser->check($testPassword));
        
        $modelUser = Factory::instance()->createModelUser();
        $modelUser->login = $testLogin;
        $modelUser->name = $testName;
        $this->assertFalse($modelUser->check($testPassword));
        $modelUser->password = $testPassword;
        $this->assertTrue($modelUser->check($testPassword));
    }
    
    public function testSetRole(): void {
        $modelUser = Factory::instance()->createModelUser();
        $modelUser->login = 'testLogin';
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
        $modelUserSave = Factory::instance()->createModelUser();
        $modelUserSave->login = 'testLogin';
        $modelUserSave->name = 'Test Name';
        $idSave = $modelUserSave->save();
        $this->assertTrue(boolval($idSave));
        $dataAfterSave = $modelUserSave->getDataAssoc();
        
        $modelUserGet = Factory::instance()->createModelUser();
        $modelUserSave->login = 'testLogin 2';
        $modelUserSave->name = 'Test Name 2';
        $modelUserGet->loadById($idSave);
        $dataAfterGet = $modelUserGet->getDataAssoc();
        
        $this->assertEquals($dataAfterSave, $dataAfterGet);
        
        $modelUserGet->login .= '3';
        $modelUserGet->name .= '!';
        $idGet = $modelUserGet->save();
        $dataAfterUpdated = $modelUserGet->getDataAssoc();
        
        $modelUserUpdatedGet = Factory::instance()->createModelUser();
        $modelUserSave->login = 'testLogin 3';
        $modelUserSave->name = 'Test Name 3';
        $modelUserUpdatedGet->loadById($idGet);
        
        $dataAfterUpdatedGet = $modelUserUpdatedGet->getDataAssoc();
        
        $this->assertEquals($dataAfterUpdated, $dataAfterUpdatedGet);
    }
    
}
