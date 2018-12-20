<?php

declare(strict_types=1);

namespace classroom;

use PHPUnit\Framework\TestCase;

include_once(dirname(__FILE__) . '/../init.php');

final class CoreTest extends TestCase {
    
    public function testGetApplicationType(): void {
        $this->assertEquals(Core::getApplicationType(), 'Client');
    }
    
    public function testGetNamespace(): void {
        $this->assertEquals(Core::getNamespace(), 'classroom\\');
    }
    
    public function testLoadClass(): void {
        $this->assertTrue(Core::loadClass('Factory', 'Module/Factory'));
        $this->assertTrue(Core::loadClass('Router', 'Module/Router'));
        
        $this->assertTrue(Core::loadClass('ModelLesson', 'Model'));
        $this->assertTrue(Core::loadClass('ModelLog', 'Model'));
        $this->assertTrue(Core::loadClass('ModelRequest', 'Model'));
        $this->assertTrue(Core::loadClass('ModelUser', 'Model'));
        $this->assertTrue(Core::loadClass('ModelWord', 'Model'));
    }
    
}
