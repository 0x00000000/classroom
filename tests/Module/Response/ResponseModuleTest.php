<?php

declare(strict_types=1);

namespace ClassroomTest\Response\Response;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

include_once(dirname(__FILE__) . '/../../init.php');

final class ResponseModuleTest extends TestCase {
    
    protected $_response;
    
    public function setUp(): void {
        $this->_response = Factory::instance()->createResponse();
    }
    
    public function testGetSetHeader(): void {
        $this->_response->setHeader('Location', 'http://test.example.com/');
        $this->_response->setHeader('Content-Type', 'application/pdf');
        $this->_response->setHeader('HTTP/1.0 404 Not Found');
        $this->assertEquals($this->_response->getHeader('Location'), 'http://test.example.com/');
        $this->assertEquals($this->_response->getHeader('Content-Type'), 'application/pdf');
        $this->assertNull($this->_response->getHeader('HTTP/1.0 404 Not Found'));
    }
    
    public function testGetSetBody(): void {
        $body = '<html>
<body>
<div class="test">
    test
</div>
</body>
</html>';
        $this->_response->setBody($body);
        $this->assertEquals($this->_response->getBody(), $body);
    }
    
}
