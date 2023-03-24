<?php

declare(strict_types=1);

namespace ClassroomTest\System;

use PHPUnit\Framework\TestCase;

use Classroom\System\Core;

include_once(dirname(__FILE__) . '/../init.php');

final class CoreTest extends TestCase {
    
    public function testSetApplicationType(): void {
        // setApplicationType was called above.
        // The second call will return false.
        $this->assertFalse(Core::setApplicationType('Client'));
    }
    
    public function testGetApplicationType(): void {
        $this->assertEquals(Core::getApplicationType(), 'Client');
    }

    public function testGetNamespacePrefix(): void {
        $this->assertEquals(Core::getNamespacePrefix(), 'Classroom\\');
    }

    public function testGetModuleClassName(): void {
        $moduleName = 'DatabaseMysql';
        $moduleBaseName = 'Database';
        $expected = 'Classroom\Module\Database\DatabaseMysql';
        $this->assertEquals(Core::getModuleClassName($moduleName, $moduleBaseName), $expected);

        $moduleName = 'Logger';
        $expected = 'Classroom\Module\Logger\Logger';
        $this->assertEquals(Core::getModuleClassName($moduleName), $expected);
    }

    public function testGetControllerClassName(): void {
        $controllerPrefix = 'Admin/ControllerAdminIndex';
        $expected = 'Classroom\Controller\Admin\ControllerAdminIndex';
        $this->assertEquals(Core::getControllerClassName($controllerPrefix), $expected);

        $controllerPrefix = 'ControllerPage';
        $expected = 'Classroom\Controller\ControllerPage';
        $this->assertEquals(Core::getControllerClassName($controllerPrefix), $expected);
    }

    public function testGetModelClassName(): void {
        $modelName = 'Page';
        $expected = 'Classroom\Model\ModelPage';
        $this->assertEquals(Core::getModelClassName($modelName), $expected);
    }

    public function testLoadClass(): void {
        $data = [
            [
                'className' => 'DatabaseMysql',
                'path' => 'Module/Database',
            ],
            [
                'className' => 'Logger',
                'path' => 'Module/Logger',
            ],
            [
                'className' => 'ControllerAdminIndex',
                'path' => 'Controller/Admin',
            ],
            [
                'className' => 'ControllerPage',
                'path' => 'Controller',
            ],
            [
                'className' => 'ModelPage',
                'path' => 'Model',
            ],
        ];

        foreach ($data as $item) {
            $this->assertTrue(Core::loadClass($item['className'], $item['path']));
        }

        $data = [
            [
                'className' => 'DatabaseNotExists',
                'path' => 'Module/Database',
            ],
            [
                'className' => 'Logger',
                'path' => 'DirectoryNotExists',
            ],
        ];
        foreach ($data as $item) {
            $this->assertFalse(Core::loadClass($item['className'], $item['path']));
        }
    }
}
