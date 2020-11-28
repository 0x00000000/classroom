<?php

declare(strict_types=1);

namespace ClassroomTest\Router\Router;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

include_once(dirname(__FILE__) . '/../../init.php');

final class RouterModuleTest extends TestCase {
    
    protected $_router = null;
    
    protected $_request = null;
    
    protected $_response = null;
    
    public function __construct() {
        parent::__construct();
        
        $this->_request = Factory::instance()->createRequest();
        
        $this->_response = Factory::instance()->createResponse();
        
        $this->_router = Factory::instance()->createRouter($this->_request, $this->_response);
    }
    
    public function testSetRule(): void {
        
        $rule = 'index';
        $rule2 = 'index/test';
        $controller = 'ControllerClient';
        $action = 'index';
        $action2 = 'indexTest';
        
        $this->assertFalse($this->_router->setRule('', $controller, $action));
        
        $this->assertFalse($this->_router->setRule($rule, '', $action));
        
        $this->assertFalse($this->_router->setRule($rule, $controller, ''));
        
        $this->assertTrue($this->_router->setRule($rule, $controller, $action));
        
        $this->assertTrue($this->_router->setRule($rule2, $controller, $action2));
        
        $this->assertTrue($this->_router->setRule($rule, $controller, $action2));
        
    }
    
    public function testSetDefaultRule(): void {
        $controller = 'ControllerClient';
        $action = 'index';
        $action2 = 'indexTest';
        
        $this->assertFalse($this->_router->setDefaultRule('', $action));
        
        $this->assertFalse($this->_router->setDefaultRule($controller, ''));
        
        $this->assertTrue($this->_router->setDefaultRule($controller, $action));
        
        $this->assertTrue($this->_router->setDefaultRule($controller, $action2));
        
    }
    
    public function testCheckRule(): void {
        $this->_request->getCurrentRequest()->url = '/page/t_e_s_t/ID123';
        $getData = array();
        $ruleData = array('rule' => '/page/<page>/<id>');
        $checked = $this->_router->checkRule($ruleData, $getData);
        
        $this->assertTrue($checked);
        $this->assertTrue(array_key_exists('page', $getData));
        $this->assertEquals($getData['page'], 't_e_s_t');
        $this->assertTrue(array_key_exists('id', $getData));
        $this->assertEquals($getData['id'], 'ID123');
        
        $this->_request->getCurrentRequest()->url = '/page/t_e_s_t/ID123';
        $getData = array('param' => 'param2', 'id' => 'value');
        $ruleData = array('rule' => '/page/<page>/<id>');
        $checked = $this->_router->checkRule($ruleData, $getData);
        
        $this->assertTrue($checked);
        $this->assertTrue(array_key_exists('page', $getData));
        $this->assertEquals($getData['page'], 't_e_s_t');
        $this->assertTrue(array_key_exists('id', $getData));
        $this->assertEquals($getData['id'], 'ID123');
        $this->assertTrue(array_key_exists('param', $getData));
        $this->assertEquals($getData['param'], 'param2');
        
        $this->_request->getCurrentRequest()->url = '/page//ID123';
        $getData = array('test' => '2');
        $ruleData = array('rule' => '/page/<page>/<id>');
        $checked = $this->_router->checkRule($ruleData, $getData);
        $this->assertFalse($checked);
        $this->assertEquals($getData, array('test' => '2'));
    }
    
    public function testAddGetData(): void {
        $this->_request->getCurrentRequest()->get = array('param2' => '2', 'param3' => 'test');
        $getData = array('param1' => 'param', 'param3' => 'test2');
        $this->_router->addGetData($getData);
        $this->assertEquals($this->_request->getCurrentRequest()->get, array('param1' => 'param', 'param2' => '2', 'param3' => 'test2'));
    }
    
}
