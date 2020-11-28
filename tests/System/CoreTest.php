<?php

declare(strict_types=1);

namespace ClassroomTest\System;

use PHPUnit\Framework\TestCase;

use Classroom\System\Core;

include_once(dirname(__FILE__) . '/../init.php');

final class CoreTest extends TestCase {
    
    public function testGetApplicationType(): void {
        $this->assertEquals(Core::getApplicationType(), 'Client');
    }
    
    public function testGetNamespacePrefix(): void {
        $this->assertEquals(Core::getNamespacePrefix(), 'Classroom\\');
    }
    
}
