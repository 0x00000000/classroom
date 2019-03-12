<?php

declare(strict_types=1);

namespace classroom;

include_once('ApplicationBase.php');

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
        
        $router->setRule('/teacher/lesson[/<action>][/<id>]', 'Teacher/ControllerTeacherManageLesson', 'index');
        
        $router->setRule('/teacher/lessonTemplate[/<action>][/<id>]', 'Teacher/ControllerTeacherManageLessonTemplate', 'index');
        
        $router->setRule('/teacher/student[/<action>][/<id>]', 'Teacher/ControllerTeacherManageStudent', 'index');
        
        $router->setRule('/teacher/activeLesson[/<action>][/<studentId>][/<lessonId>]', 'Teacher/ControllerTeacherActiveLesson', 'index');
        
        $router->setRule('/student/lesson[/<action>][/<id>]', 'Student/ControllerStudentManageLesson', 'index');
        
        $router->setRule('/student/activeLesson[/<action>][/<teacherId>][/<lessonId>]', 'Student/ControllerStudentActiveLesson', 'index');
        
        $router->setRule('/login', 'Guest/ControllerGuestLogin', 'index');
        
        $router->setDefaultRule('Guest/ControllerGuestIndex', 'index');
        
    }
    
}
