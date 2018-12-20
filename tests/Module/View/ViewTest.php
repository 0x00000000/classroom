<?php

declare(strict_types=1);

namespace classroom;

use PHPUnit\Framework\TestCase;

include_once(dirname(__FILE__) . '/../../init.php');

final class ViewTest extends TestCase {
    
    protected $_view = null;
    
    public function __construct() {
        parent::__construct();
        
        $this->_view = Factory::instance()->createView();
        
    }
    
    public function testRender(): void {
        
        $this->assertFalse($this->_view->setTemplate('Test/index-absent'));
        
        $this->assertTrue($this->_view->setTemplate('Test/index'));
        
        $this->assertTrue($this->_view->set('Test1', '111'));
        $this->assertTrue($this->_view->set('test_2', 'Test 2'));
        
        $content = 'Simple test template.
String with param 111 .
String with another param Test 2 .
End of template.';
        $this->assertEquals($this->_view->render(), $content);
        
        $this->assertFalse($this->_view->setTemplate('Test/index-absent'));
        
        $this->assertEquals($this->_view->render(), '');
        
    }
    
}
