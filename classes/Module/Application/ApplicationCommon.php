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
        $router->setRule('/admin', 'Admin/ControllerAdmin', 'index');
        
        $router->setRule('/admin/vocabulary/images/fill', 'Admin/ControllerVocabulary', 'imagesAdd');
        
        $router->setRule('/admin/vocabulary/images/fill/<page>', 'Admin/ControllerVocabulary', 'imagesAdd');
        
        $router->setDefaultRule('Client/ControllerClient', 'index');
        
    }
    
}
