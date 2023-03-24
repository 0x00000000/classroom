<?php

declare(strict_types=1);

namespace ClassroomTest\View\View;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

include_once(dirname(__FILE__) . '/../../init.php');

final class ViewTest extends TestCase {
    
    protected $_view = null;
    
    public function setUp(): void {
        $this->_view = Factory::instance()->createView();
    }

    public function testSetTemplate(): void {
        $this->assertFalse($this->_view->setTemplate('Test/index-absent'));
        $this->assertTrue($this->_view->setTemplate('Test/index'));
    }

    public function testSet(): void {
        $this->assertTrue($this->_view->set('Test1', '111'));
        $this->assertTrue($this->_view->set('test_2', 'Test 2'));
    }

    public function testRender(): void {
        $this->_view->setTemplate('Test/index');
        $this->_view->set('Test1', '111');
        $this->_view->set('test_2', 'Test 2');
        $content = 'Simple test template.
String with param 111 .
String with another param Test 2 .
End of template.';
        $this->assertEquals($this->_view->render(), $content);
        
        $this->_view->setTemplate('Test/index-absent');
        $this->assertEquals($this->_view->render(), '');
        
    }
    
}
