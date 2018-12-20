<?php

declare(strict_types=1);

namespace classroom;

use PHPUnit\Framework\TestCase;

include_once(dirname(__FILE__) . '/../../init.php');

final class FactoryTest extends TestCase {
    
    protected $_request = null;
    
    protected $_response = null;
    
    // protected $_router = null;
    
    public function __construct() {
        parent::__construct();
        
        $this->_request = Factory::instance()->createRequest();
        
        $this->_response = Factory::instance()->createResponse();
        
        $this->_router = Factory::instance()->createRouter($this->_request, $this->_response);
    }
    
    public function testSetDatabase(): void {
        $moduleName = 'Logger';
        
        $moduleBaseName = 'Database';
        $moduleName = $moduleBaseName . 'Mysql';
        $database = Factory::instance()->createModule($moduleName, $moduleBaseName);
        $this->assertTrue(Factory::instance()->setDatabase($database));
        
        $moduleBaseName = 'Database';
        $moduleName = $moduleBaseName . 'Test';
        $database = Factory::instance()->createModule($moduleName, $moduleBaseName);
        $this->assertTrue(Factory::instance()->setDatabase($database));
    }
    
    public function testLoadController(): void {
        $this->assertTrue(Factory::instance()->loadController('ControllerClient', 'Client'));
        $this->assertTrue(Factory::instance()->loadController('ControllerTest', 'Test'));
    }
    
    public function testLoadModel(): void {
        $this->assertTrue(Factory::instance()->loadModel('ModelLesson'));
        $this->assertTrue(Factory::instance()->loadModel('ModelLog'));
        $this->assertTrue(Factory::instance()->loadModel('ModelRequest'));
        $this->assertTrue(Factory::instance()->loadModel('ModelUser'));
        $this->assertTrue(Factory::instance()->loadModel('ModelWord'));
    }
    
    public function testLoadModule(): void {
        $this->assertTrue(Factory::instance()->loadModule('Logger'));
        $this->assertTrue(Factory::instance()->loadModule('DatabaseMysql', 'Database'));
        $this->assertTrue(Factory::instance()->loadModule('Router'));
    }
    
    public function testLoadSmarty(): void {
        $this->assertTrue(Factory::instance()->loadSmarty());
    }
    
    public function testCreateModule(): void {
        $logger = Factory::instance()->createModule('Logger');
        $this->assertTrue($logger instanceof Logger);
        
        $database = Factory::instance()->createModule('DatabaseMysql', 'Database');
        $this->assertTrue($database instanceof Database);
        
        // There are no such modules.
        // $router = Factory::instance()->createTypedModule('Router');
        // $this->assertTrue($router instanceof Router));
    }
    
    public function testCreateModel(): void {
        $request = Factory::instance()->createModel('ModelRequest');
        $this->assertTrue($request instanceof Model);
        
        $log = Factory::instance()->createModel('ModelLog');
        $this->assertTrue($log instanceof Model);
        
        $request = Factory::instance()->createModelRequest();
        $this->assertTrue($request instanceof Model);
        
        $log = Factory::instance()->createModelLog($this->_request);
        $this->assertTrue($log instanceof Model);
        
    }
    
    public function testCreateController(): void {
        $controllerClient = Factory::instance()->createController('Client/ControllerClient', $this->_request, $this->_response);
        $this->assertTrue($controllerClient instanceof Controller);
        $controllerClientClient = Factory::instance()->createController('Test/ControllerTest', $this->_request, $this->_response);
        $this->assertTrue($controllerClientClient instanceof Controller);
    }
        
    public function testCreateConfig(): void {
        $config = Factory::instance()->createConfig();
        $this->assertTrue($config instanceof Config);
    }
    
    public function testCreateDatabase(): void {
        $database = Factory::instance()->createDatabase();
        $this->assertTrue($database instanceof Database);
    }
    
    public function createLogger(): void {
        $logger = Factory::instance()->createLogger($this->_modelRequest);
        $this->assertTrue($logger instanceof Logger);
    }
    
    public function createRegistry(): void {
        $registry = Factory::instance()->createRegistry();
        $this->assertTrue($registry instanceof Registry);
    }
    
    public function testCreateRequest(): void {
        $request = Factory::instance()->createRequest();
        $this->assertTrue($request instanceof Request);
    }
    
    public function testCreateResponse(): void {
        $response = Factory::instance()->createResponse();
        $this->assertTrue($response instanceof Response);
    }
    
    public function testCreateRouter(): void {
        $request = Factory::instance()->createRequest();
        $response = Factory::instance()->createResponse();
        $router = Factory::instance()->createRouter($request, $response);
        $this->assertTrue($router instanceof Router);
    }
    
    public function testCreateSmarty(): void {
        $smarty = Factory::instance()->createSmarty();
        $this->assertTrue($smarty instanceof \Smarty);
    }
    
    public function testCreateView(): void {
        $view = Factory::instance()->createView();
        $this->assertTrue($view instanceof View);
    }
    
}
