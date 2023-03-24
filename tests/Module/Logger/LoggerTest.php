<?php

declare(strict_types=1);

namespace ClassroomTest\Logger\Logger;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

include_once(dirname(__FILE__) . '/../../init.php');

final class LoggerTest extends TestCase {
    protected $_logger = null;
    
    protected $_request = null;

    protected function setUp(): void {
        $this->_request = Factory::instance()->createRequest();
        $this->_logger = Factory::instance()->createLogger($this->_request);
    }

    public function testLogCritical(): void {
        $result = $this->_logger->logCritical(
            'Testing log critical caption', 'Testing log critical caption description'
        );
        
        $this->assertTrue($result);
    }
    
    public function testLogError(): void {
        $result = $this->_logger->logError(
            'Testing log error caption', 'Testing log error caption description'
        );
        
        $this->assertTrue($result);
    }
    
    public function testLogWarning(): void {
        $result = $this->_logger->logWarning(
            'Testing log warning caption', 'Testing log warning caption description'
        );
        
        $this->assertTrue($result);
    }
    
    public function testLogNotice(): void {
        $result = $this->_logger->logNotice(
            'Testing log notice caption', 'Testing log notice caption description'
        );
        
        $this->assertTrue($result);
    }
    
    public function testSetRequest(): void {
        $logger = Factory::instance()->createModule('Logger');

        $this->assertTrue($logger->setRequest($this->_request));
    }
    
}
