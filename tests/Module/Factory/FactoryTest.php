<?php

declare(strict_types=1);

namespace ClassroomTest\Factory\Factory;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

use Classroom\Model\Model;

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
        $this->assertTrue(Factory::instance()->loadController('ControllerGuestIndex', 'Guest'));
        $this->assertTrue(Factory::instance()->loadController('ControllerTestIndex', 'Test'));
    }
    
    public function testLoadModel(): void {
        $this->assertTrue(Factory::instance()->loadModel('Lesson'));
        $this->assertTrue(Factory::instance()->loadModel('Log'));
        $this->assertTrue(Factory::instance()->loadModel('Request'));
        $this->assertTrue(Factory::instance()->loadModel('User'));
        $this->assertTrue(Factory::instance()->loadModel('Word'));
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
        $this->assertTrue($logger instanceof \Classroom\Module\Logger\Logger);
        
        $database = Factory::instance()->createModule('DatabaseMysql', 'Database');
        $this->assertTrue($database instanceof \Classroom\Module\Database\Database);
        
        // There are no such modules.
        // $router = Factory::instance()->createTypedModule('Router');
        // $this->assertTrue($router instanceof \Classroom\Module\Router\Router));
    }
    
    public function testCreateModel(): void {
        $request = Factory::instance()->createModel('Request');
        $this->assertTrue($request instanceof \Classroom\Model\Model);
        
        $log = Factory::instance()->createModel('Log');
        $this->assertTrue($log instanceof \Classroom\Model\Model);
        
        $request = Factory::instance()->createModel('Request');
        $this->assertTrue($request instanceof \Classroom\Model\Model);
        
        $log = Factory::instance()->createModel('Log');
        $log->setRequest($this->_request);
        $this->assertTrue($log instanceof \Classroom\Model\Model);
        
    }
    
    public function testCreateController(): void {
        $controllerGuest = Factory::instance()->createController('Guest/ControllerGuestIndex', $this->_request, $this->_response);
        $this->assertTrue($controllerGuest instanceof \Classroom\Controller\Controller);
        $controllerTest = Factory::instance()->createController('Test/ControllerTestIndex', $this->_request, $this->_response);
        $this->assertTrue($controllerTest instanceof \Classroom\Controller\Controller);
    }
        
    public function testCreateAuth(): void {
        $auth = Factory::instance()->createAuth($this->_request);
        $this->assertTrue($auth instanceof \Classroom\Module\Auth\Auth);
    }
    
    public function testCreateConfig(): void {
        $config = Factory::instance()->createConfig();
        $this->assertTrue($config instanceof \Classroom\Module\Config\Config);
    }
    
    public function testCreateDatabase(): void {
        $database = Factory::instance()->createDatabase();
        $this->assertTrue($database instanceof \Classroom\Module\Database\Database);
    }
    
    public function createLogger(): void {
        $logger = Factory::instance()->createLogger($this->_modelRequest);
        $this->assertTrue($logger instanceof \Classroom\Module\Logger\Logger);
    }
    
    public function createRegistry(): void {
        $registry = Factory::instance()->createRegistry();
        $this->assertTrue($registry instanceof \Classroom\Module\Registry\Registry);
    }
    
    public function testCreateRequest(): void {
        $request = Factory::instance()->createRequest();
        $this->assertTrue($request instanceof \Classroom\Module\Request\Request);
    }
    
    public function testCreateResponse(): void {
        $response = Factory::instance()->createResponse();
        $this->assertTrue($response instanceof \Classroom\Module\Response\Response);
    }
    
    public function testCreateRouter(): void {
        $request = Factory::instance()->createRequest();
        $response = Factory::instance()->createResponse();
        $router = Factory::instance()->createRouter($request, $response);
        $this->assertTrue($router instanceof \Classroom\Module\Router\Router);
    }
    
    public function testCreateSmarty(): void {
        $smarty = Factory::instance()->createSmarty();
        $this->assertTrue($smarty instanceof \Smarty);
    }
    
    public function testCreateView(): void {
        $view = Factory::instance()->createView();
        $this->assertTrue($view instanceof \Classroom\Module\View\View);
    }
    
}
