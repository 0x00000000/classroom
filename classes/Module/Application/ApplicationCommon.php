<?php

declare(strict_types=1);

namespace Classroom\Module\Application;

use Classroom\Module\Factory\Factory;

/**
 * Facade for other modules.
 * Runs client UI application.
 */
class ApplicationCommon extends ApplicationBase {
    
    /**
     * Runs application.
     */
    public function run(): void {
        
        $request = Factory::instance()->createRequest();
        
        $response = Factory::instance()->createResponse();
        
        $router = Factory::instance()->createRouter($request, $response);
        
        $this->setRoutes($router);
        
        $router->route();
        
    }
    
    public function setRoutes($router): void {
        $router->setRule('/admin', 'Admin/ControllerAdminIndex', 'index');
        
        $router->setRule('/admin/vocabulary/images/fill', 'Admin/ControllerAdminVocabulary', 'imagesAdd');
        
        $router->setRule('/admin/vocabulary/images/fill/<page>', 'Admin/ControllerAdminVocabulary', 'imagesAdd');
        
        $router->setRule('/admin/menu[/<action>][/<id>]', 'Admin/ControllerAdminManageMenu', 'index');
        
        $router->setRule('/admin/menuItem[/<action>][/<id>]', 'Admin/ControllerAdminManageMenuItem', 'index');
        
        $router->setRule('/admin/page[/<action>][/<id>]', 'Admin/ControllerAdminManagePage', 'index');
        
        $router->setRule('/admin/teacher[/<action>][/<id>]', 'Admin/ControllerAdminManageTeacher', 'index');
        
        $router->setRule('/teacher', 'Teacher/ControllerTeacherIndex', 'index');
        
        $router->setRule('/teacher/lesson[/<action>][/<id>]', 'Teacher/ControllerTeacherManageLesson', 'index');
        
        $router->setRule('/teacher/lessonTemplate[/<action>][/<id>]', 'Teacher/ControllerTeacherManageLessonTemplate', 'index');
        
        $router->setRule('/teacher/student[/<action>][/<id>]', 'Teacher/ControllerTeacherManageStudent', 'index');
        
        $router->setRule('/teacher/activeLesson[/<action>][/<studentId>][/<lessonId>]', 'Teacher/ControllerTeacherActiveLesson', 'index');
        
        $router->setRule('/teacher/contentImage', 'Teacher/ControllerTeacherContentImage', 'index');
        
        $router->setRule('/student', 'Student/ControllerStudentIndex', 'index');
        
        $router->setRule('/student/lesson[/<action>][/<id>]', 'Student/ControllerStudentManageLesson', 'index');
        
        $router->setRule('/student/activeLesson[/<action>][/<teacherId>][/<lessonId>]', 'Student/ControllerStudentActiveLesson', 'index');
        
        $router->setRule('/profile[/<action>]', 'User/ControllerUserProfile', 'index');
        
        $router->setRule('/login', 'User/ControllerUserLogin', 'index');
        
        $router->setDefaultRule('ControllerPage', 'index');
        
    }
    
}
