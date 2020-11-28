<?php

declare(strict_types=1);

namespace ClassroomTest\System;

use PHPUnit\Framework\TestCase;

use Classroom\System\Filesystem;

include_once(dirname(__FILE__) . '/../init.php');

final class FileSystemTest extends TestCase {
    
    public function testGetRoot(): void {
        $this->assertEquals(FileSystem::getRoot(), dirname(dirname(dirname(__FILE__))));
    }
    
    public function testGetDataDir(): void {
        $this->assertEquals(FileSystem::getDataDir(), dirname(dirname(dirname(__FILE__))) . FileSystem::getDS() . 'data');
    }
    
    public function testGetDirectorySeparator(): void {
        $this->assertEquals(FileSystem::getDirectorySeparator(), DIRECTORY_SEPARATOR);
    }
    
    public function testGetScriptExtension(): void {
        $this->assertEquals(FileSystem::getScriptExtension(), '.php');
    }
    
}
