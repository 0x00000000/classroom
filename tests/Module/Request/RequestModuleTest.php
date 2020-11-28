<?php

declare(strict_types=1);

namespace ClassroomTest\Request\Request;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

use Classroom\Module\Request\RequestTest;

include_once(dirname(__FILE__) . '/../../init.php');

final class RequestModuleTest extends TestCase {
    
    protected $_request;
    
    public function __construct() {
        parent::__construct();
        
        $this->_request = Factory::instance()->createRequest();
        
    }
    
    public function testGetCurrentRequest(): void {
        $currentRequest = $this->_request->getCurrentRequest();
        $this->assertTrue($currentRequest instanceof \Classroom\Model\ModelRequest);
        $this->assertEquals($currentRequest->url, RequestTest::TEST_URL);
        $this->assertEquals($currentRequest->get, RequestTest::TEST_GET);
        $this->assertEquals($currentRequest->post, RequestTest::TEST_POST);
        $this->assertEquals($currentRequest->session, RequestTest::TEST_SESSION);
        $this->assertEquals($currentRequest->headers, RequestTest::TEST_HEADERS);
        $this->assertEquals($currentRequest->ip, RequestTest::TEST_IP);
        $this->assertEquals($currentRequest->userAgent, RequestTest::TEST_USER_AGENT);
        $this->assertNull($currentRequest->info);
    }
    
    public function testData(): void {
        $this->assertEquals($this->_request->url, RequestTest::TEST_URL);
        $this->assertEquals($this->_request->get, RequestTest::TEST_GET);
        $this->assertEquals($this->_request->post, RequestTest::TEST_POST);
        $this->assertEquals($this->_request->session, RequestTest::TEST_SESSION);
        $this->assertEquals($this->_request->headers, RequestTest::TEST_HEADERS);
        $this->assertEquals($this->_request->ip, RequestTest::TEST_IP);
        $this->assertEquals($this->_request->userAgent, RequestTest::TEST_USER_AGENT);
        $this->assertNull($this->_request->info);
    }
    
}
