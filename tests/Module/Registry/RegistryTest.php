<?php

declare(strict_types=1);

namespace classroom;

use PHPUnit\Framework\TestCase;

include_once(dirname(__FILE__) . '/../../init.php');

final class RegistryTest extends TestCase {
    
    public function testSetGet(): void {
        $values[] = array('1', true, false, null, 'Test', "Test\nstring",);
        $keyArray = 'keyArray';
        $valueArray = array(1, 2, 'key' => 'val');
        
        $registry = Factory::instance()->createRegistry();
        
        for ($i = 0; $i < count($values); $i++) {
            $registry->set('key' . $i, $values[$i]);
            $testValue = $registry->get('key' . $i);
            $this->assertEquals($values[$i],  $testValue);
        }
        
        $registry->set($keyArray, $valueArray);
        $testValue = $registry->get($keyArray);
        $this->assertEquals($testValue,  $valueArray);
    }
    
}
