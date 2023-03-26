<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;
use ClassroomTest\UsesModelUserTrait;

abstract class ModelAccessRights extends ModelDatabase {
    use UsesModelUserTrait;

    public function testUserHasAccess(): void {
        $request = Factory::instance()->createRequest();
        $auth = Factory::instance()->createAuth($request);
        $password = $this->getUniquePassword();
        $admin = $this->createUserAdmin($password);
        $teacher = $this->createUserTeacher($password);
        $student = $this->createUserStudent($password);

        $modelAccessAdmin = $this->createModelAccessAdmin();
        $modelAccessTeacher = $this->createModelAccessTeacher();
        $modelAccessStudent = $this->createModelAccessStudent();
        $modelAccessGuest = $this->createModelAccessGuest();

        $auth->login(
            $admin->login,
            $password
        );
        $this->assertTrue($modelAccessAdmin->userHasAccess($auth));
        $this->assertFalse($modelAccessTeacher->userHasAccess($auth));
        $this->assertFalse($modelAccessStudent->userHasAccess($auth));
        $this->assertFalse($modelAccessGuest->userHasAccess($auth));
        $auth->logout();

        $auth->login(
            $teacher->login,
            $password
        );
        $this->assertFalse($modelAccessAdmin->userHasAccess($auth));
        $this->assertTrue($modelAccessTeacher->userHasAccess($auth));
        $this->assertFalse($modelAccessStudent->userHasAccess($auth));
        $this->assertFalse($modelAccessGuest->userHasAccess($auth));
        $auth->logout();

        $auth->login(
            $student->login,
            $password
        );
        $this->assertFalse($modelAccessAdmin->userHasAccess($auth));
        $this->assertFalse($modelAccessTeacher->userHasAccess($auth));
        $this->assertTrue($modelAccessStudent->userHasAccess($auth));
        $this->assertFalse($modelAccessGuest->userHasAccess($auth));
        $auth->logout();

        $this->assertFalse($modelAccessAdmin->userHasAccess($auth));
        $this->assertFalse($modelAccessTeacher->userHasAccess($auth));
        $this->assertFalse($modelAccessStudent->userHasAccess($auth));
        $this->assertTrue($modelAccessGuest->userHasAccess($auth));
    }

    private function createUserAdmin($password): \Classroom\Model\ModelUser {
        $modelUser = $this->createUser(
            $this->getUniqueLogin(),
            $password
        );
        $modelUser->isAdmin = true;
        $modelUser->isTeacher = false;
        $modelUser->isStudent = false;
        $modelUser->save();
        return $modelUser;
    }

    private function createUserTeacher($password): \Classroom\Model\ModelUser {
        $modelUser = $this->createUser(
            $this->getUniqueLogin(),
            $password
        );
        $modelUser->isAdmin = false;
        $modelUser->isTeacher = true;
        $modelUser->isStudent = false;
        $modelUser->save();
        return $modelUser;
    }

    private function createUserStudent($password): \Classroom\Model\ModelUser {
        $modelUser = $this->createUser(
            $this->getUniqueLogin(),
            $password
        );
        $modelUser->isAdmin = false;
        $modelUser->isTeacher = false;
        $modelUser->isStudent = true;
        $modelUser->save();
        return $modelUser;
    }

    private function createModelAccessAdmin(): \Classroom\Model\ModelAccessRights {
        $model = Factory::instance()->createModel($this->_modelName);
        $model->accessAdmin = true;
        $model->accessTeacher = false;
        $model->accessStudent = false;
        $model->accessGuest = false;
        return $model;
    }

    private function createModelAccessTeacher(): \Classroom\Model\ModelAccessRights {
        $model = Factory::instance()->createModel($this->_modelName);
        $model->accessAdmin = false;
        $model->accessTeacher = true;
        $model->accessStudent = false;
        $model->accessGuest = false;
        return $model;
    }

    private function createModelAccessStudent(): \Classroom\Model\ModelAccessRights {
        $model = Factory::instance()->createModel($this->_modelName);
        $model->accessAdmin = false;
        $model->accessTeacher = false;
        $model->accessStudent = true;
        $model->accessGuest = false;
        return $model;
    }

    private function createModelAccessGuest(): \Classroom\Model\ModelAccessRights {
        $model = Factory::instance()->createModel($this->_modelName);
        $model->accessAdmin = false;
        $model->accessTeacher = false;
        $model->accessStudent = false;
        $model->accessGuest = true;
        return $model;
    }
}
