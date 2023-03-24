<?php

declare(strict_types=1);

namespace ClassroomTest\Request\Request;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

use Classroom\Module\Request\RequestTest;

include_once(dirname(__FILE__) . '/../../init.php');

final class RequestModuleTest extends TestCase {
    
    protected $_request;

    protected function setUp(): void {
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
    
    public function testRequestData(): void {
        $this->assertEquals($this->_request->url, RequestTest::TEST_URL);
        $this->assertEquals($this->_request->get, RequestTest::TEST_GET);
        $this->assertEquals($this->_request->post, RequestTest::TEST_POST);
        $this->assertEquals($this->_request->session, RequestTest::TEST_SESSION);
        $this->assertEquals($this->_request->headers, RequestTest::TEST_HEADERS);
        $this->assertEquals($this->_request->ip, RequestTest::TEST_IP);
        $this->assertEquals($this->_request->userAgent, RequestTest::TEST_USER_AGENT);
        $this->assertNull($this->_request->info);
    }

    public function testSetSessionVariable(): void {
        $falseKeysList = ['', '0'];
        $valueCommon = 'valueCommon';
        foreach ($falseKeysList as $key) {
            $result = $this->_request->setSessionVariable($key, $valueCommon);
            $this->assertFalse($result);
        }

        $data = [
            'keyString' => 'valueString',
            'keyStringEmpty' => 'valueString',
            'keyInt' => 15,
            'keyInt0' => 15,
            'keyTrue' => true,
            'keyFalse' => false,
            'keyNull' => null,
        ];
        foreach ($data as $key => $value) {
            $result = $this->_request->setSessionVariable($key, $value);
            $this->assertTrue($result);
            $this->assertEquals($this->_request->session[$key], $value);
        }
    }

    public function testUnsetSessionVariable(): void {
        $falseKeysList = ['', '0'];
        $valueCommon = 'valueCommon';
        foreach ($falseKeysList as $key) {
            $result = $this->_request->unsetSessionVariable($key);
            $this->assertFalse($result);
        }

        $notExistedKey = 'notExistedKey';
        $result = $this->_request->unsetSessionVariable($notExistedKey);
        $this->assertTrue($result);

        $data = [
            'keyString' => 'valueString',
            'keyStringEmpty' => 'valueString',
            'keyInt' => 15,
            'keyInt0' => 15,
            'keyTrue' => true,
            'keyFalse' => false,
            'keyNull' => null,
        ];

        foreach ($data as $key => $value) {
            $this->_request->setSessionVariable($key, $value);
            $result = $this->_request->unsetSessionVariable($key);
            $this->assertTrue($result);
            $this->assertFalse(array_key_exists($key, $this->_request->session));
        }
    }

}
