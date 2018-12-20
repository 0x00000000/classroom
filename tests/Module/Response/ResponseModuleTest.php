<?php

declare(strict_types=1);

namespace classroom;

use PHPUnit\Framework\TestCase;

include_once(dirname(__FILE__) . '/../../init.php');

final class ResponseModuleTest extends TestCase {
    
    protected $_response;
    
    public function __construct() {
        parent::__construct();
        
        $this->_response = Factory::instance()->createResponse();
        
    }
    
    public function testHeader(): void {
        $this->_response->setHeader('Location', 'http://test.example.com/');
        $this->_response->setHeader('Content-Type', 'application/pdf');
        $this->_response->setHeader('HTTP/1.0 404 Not Found');
        $this->assertEquals($this->_response->getHeader('Location'), 'http://test.example.com/');
        $this->assertEquals($this->_response->getHeader('Content-Type'), 'application/pdf');
        $this->assertNull($this->_response->getHeader('HTTP/1.0 404 Not Found'));
    }
    
    public function testBody(): void {
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
