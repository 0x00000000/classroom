<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

use Classroom\Module\Factory\Factory;
use Classroom\Model\ModelRequest;

include_once(dirname(__FILE__) . '/../init.php');

final class ModelRequestTest extends ModelDatabase {
    protected string $_modelName = 'Request';

    private $_modelRequest = null;
    
    public function setUp(): void {
        $this->_modelRequest = Factory::instance()->createModel('Request');
    }
    
    public function testCreate(): void {
        $id = $this->_modelRequest->create();
        
        $this->assertEquals($this->_modelRequest->url, ModelRequest::UNKNOWN_REQUEST_URI);
    }
    
    public function testSetGetGet(): void {
        $data = array();
        $this->_modelRequest->setGet($data);
        $testData = $this->_modelRequest->getGet();
        $this->assertEquals($data, $testData);
        
        $data = array('1', 'two', false, true);
        $this->_modelRequest->setGet($data);
        $testData = $this->_modelRequest->getGet();
        $this->assertEquals($data, $testData);
        
        $data = array('1', 'two', false, true, array(), array(0, '1', 2));
        $this->_modelRequest->setGet($data);
        $testData = $this->_modelRequest->getGet();
        $this->assertEquals($data, $testData);
        
        $data = array('a' => '1', 'b' => 'two', 'c' => false, 'd' => true, 'e' => array(), 'f' => array('x' => 0, 'y' => '1', 'z' => 2));
        $this->_modelRequest->setGet($data);
        $testData = $this->_modelRequest->getGet();
        $this->assertEquals($data, $testData);
        
    }
    
    public function testSetGetPost(): void {
        $data = array();
        $this->_modelRequest->setPost($data);
        $testData = $this->_modelRequest->getPost();
        $this->assertEquals($data, $testData);
        
        $data = array('1', 'two', false, true);
        $this->_modelRequest->setPost($data);
        $testData = $this->_modelRequest->getPost();
        $this->assertEquals($data, $testData);
        
        $data = array('1', 'two', false, true, array(), array(0, '1', 2));
        $this->_modelRequest->setPost($data);
        $testData = $this->_modelRequest->getPost();
        $this->assertEquals($data, $testData);
        
        $data = array('a' => '1', 'b' => 'two', 'c' => false, 'd' => true, 'e' => array(), 'f' => array('x' => 0, 'y' => '1', 'z' => 2));
        $this->_modelRequest->setPost($data);
        $testData = $this->_modelRequest->getPost();
        $this->assertEquals($data, $testData);
        
    }
    
    public function testSetGetSession(): void {
        $data = array();
        $this->_modelRequest->setSession($data);
        $testData = $this->_modelRequest->getSession();
        $this->assertEquals($data, $testData);
        
        $data = array('1', 'two', false, true);
        $this->_modelRequest->setSession($data);
        $testData = $this->_modelRequest->getSession();
        $this->assertEquals($data, $testData);
        
        $data = array('1', 'two', false, true, array(), array(0, '1', 2));
        $this->_modelRequest->setSession($data);
        $testData = $this->_modelRequest->getSession();
        $this->assertEquals($data, $testData);
        
        $data = array('a' => '1', 'b' => 'two', 'c' => false, 'd' => true, 'e' => array(), 'f' => array('x' => 0, 'y' => '1', 'z' => 2));
        $this->_modelRequest->setSession($data);
        $testData = $this->_modelRequest->getSession();
        $this->assertEquals($data, $testData);
        
    }
    
    public function testSetGetHeaders(): void {
        $data = array();
        $this->_modelRequest->setHeaders($data);
        $testData = $this->_modelRequest->getHeaders();
        $this->assertEquals($data, $testData);
        
        $data = array('1', 'two', false, true);
        $this->_modelRequest->setHeaders($data);
        $testData = $this->_modelRequest->getHeaders();
        $this->assertEquals($data, $testData);
        
        $data = array('1', 'two', false, true, array(), array(0, '1', 2));
        $this->_modelRequest->setHeaders($data);
        $testData = $this->_modelRequest->getHeaders();
        $this->assertEquals($data, $testData);
        
        $data = array('a' => '1', 'b' => 'two', 'c' => false, 'd' => true, 'e' => array(), 'f' => array('x' => 0, 'y' => '1', 'z' => 2));
        $this->_modelRequest->setHeaders($data);
        $testData = $this->_modelRequest->getHeaders();
        $this->assertEquals($data, $testData);
        
    }
    
    public function testCostants(): void {
        $this->assertTrue(! empty(ModelRequest::UNKNOWN_REQUEST_URI));
    }

    protected function getTestData(): array {
        return [
            [
                'url' => '/test1',
                'get' => array('varGet' => 11),
                'post' => array('varPost' => 12),
                'files' => array('varFiles' => 13),
                'session' => array('varSession' => 14),
                'headers' => array('varHeaders' => 15),
                'ip' => '192.168.0.1',
                'userAgent' => 'Chrome',
                'info' => 'Test info 1',
            ],
            [
                'url' => '/test2',
                'get' => array('varGet' => 21),
                'post' => array('varPost' => 22),
                'files' => array('varFiles' => 23),
                'session' => array('varSession' => 24),
                'headers' => array('varHeaders' => 25),
                'ip' => '192.168.0.2',
                'userAgent' => 'Firefox',
                'info' => 'Test info 2',
            ],
        ];
    }
}
