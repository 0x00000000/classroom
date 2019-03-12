<?php

declare(strict_types=1);

namespace classroom;

use PHPUnit\Framework\TestCase;

include_once(dirname(__FILE__) . '/../init.php');

Factory::instance()->loadModel('Log');

final class ModelLogTest extends TestCase {
    protected $_modelLog = null;
    protected $_request = null;
    protected $_logData = null;
    
    public function __construct() {
        parent::__construct();
        
        $this->_request = Factory::instance()->createRequest();
        
        $this->_modelLog = Factory::instance()->createModel('Log');
        $this->_modelLog->setRequest($this->_request);
        
        $this->_logData = $this->getTestData();
    }
    
    public function testCreate(): void {
        $id = $this->_modelLog->create(
            $this->_logData['level'], $this->_logData['message'], $this->_logData['description'],
            $this->_logData['data'],
            $this->_logData['code'], $this->_logData['file'], $this->_logData['line'], $this->_logData['url']
        );
        
        $this->assertEquals($this->_modelLog->level, $this->_logData['level']);
        $this->assertEquals($this->_modelLog->message, $this->_logData['message']);
        $this->assertEquals($this->_modelLog->description, $this->_logData['description']);
        $this->assertEquals($this->_modelLog->data, $this->_logData['data']);
        $this->assertEquals($this->_modelLog->code, $this->_logData['code']);
        $this->assertEquals($this->_modelLog->file, $this->_logData['file']);
        $this->assertEquals($this->_modelLog->line, $this->_logData['line']);
        $this->assertEquals($this->_modelLog->url, $this->_logData['url']);
        
        $this->assertEquals($this->_modelLog->requestId, $this->_request->getCurrentRequest()->id);
    }
    
    public function testData(): void {
        $this->_modelLog->create(
            $this->_logData['level'], $this->_logData['message'], $this->_logData['description'],
            $this->_logData['data'],
            $this->_logData['code'], $this->_logData['file'], $this->_logData['line'], $this->_logData['url']
        );
        
        $data = array();
        $this->_modelLog->setData($data);
        $testData = $this->_modelLog->getData();
        $this->assertEquals($data, $testData);
        
        $data = array('1', 'two', false, true);
        $this->_modelLog->setData($data);
        $testData = $this->_modelLog->getData();
        $this->assertEquals($data, $testData);
        
        $data = array('1', 'two', false, true, array(), array(0, '1', 2));
        $this->_modelLog->setData($data);
        $testData = $this->_modelLog->getData();
        $this->assertEquals($data, $testData);
        
        $data = array('a' => '1', 'b' => 'two', 'c' => false, 'd' => true, 'e' => array(), 'f' => array('x' => 0, 'y' => '1', 'z' => 2));
        $this->_modelLog->setData($data);
        $testData = $this->_modelLog->getData();
        $this->assertEquals($data, $testData);
    }
    
    public function testCritical(): void {
        $this->_modelLog->createCritical(
            $this->_logData['message'], $this->_logData['description'],
            $this->_logData['data'],
            $this->_logData['code'], $this->_logData['file'], $this->_logData['line'], $this->_logData['url']
        );
        
        $this->assertEquals($this->_modelLog->level, ModelLog::LEVEL_CRITICAL);
        
        $this->_modelLog->createCritical(
            $this->_logData['message'], $this->_logData['description'],
            $this->_logData['data'],
            null, $this->_logData['file'], $this->_logData['line'], $this->_logData['url']
        );
        
        $this->assertEquals($this->_modelLog->level, ModelLog::LEVEL_CRITICAL);
        $this->assertEquals($this->_modelLog->code, E_USER_ERROR);
    }
    
    public function testError(): void {
        $this->_modelLog->createError(
            $this->_logData['message'], $this->_logData['description'],
            $this->_logData['data'],
            $this->_logData['code'], $this->_logData['file'], $this->_logData['line'], $this->_logData['url']
        );
        
        $this->assertEquals($this->_modelLog->level, ModelLog::LEVEL_ERROR);
        
        $this->_modelLog->createError(
            $this->_logData['message'], $this->_logData['description'],
            $this->_logData['data'],
            null, $this->_logData['file'], $this->_logData['line'], $this->_logData['url']
        );
        
        $this->assertEquals($this->_modelLog->level, ModelLog::LEVEL_ERROR);
        $this->assertEquals($this->_modelLog->code, E_USER_ERROR);
    }
    
    public function testWarning(): void {
        $this->_modelLog->createWarning(
            $this->_logData['message'], $this->_logData['description'],
            $this->_logData['data'],
            $this->_logData['code'], $this->_logData['file'], $this->_logData['line'], $this->_logData['url']
        );
        
        $this->assertEquals($this->_modelLog->level, ModelLog::LEVEL_WARNING);
        
        $this->_modelLog->createWarning(
            $this->_logData['message'], $this->_logData['description'],
            $this->_logData['data'],
            null, $this->_logData['file'], $this->_logData['line'], $this->_logData['url']
        );
        
        $this->assertEquals($this->_modelLog->level, ModelLog::LEVEL_WARNING);
        $this->assertEquals($this->_modelLog->code, E_USER_WARNING);
    }
    
    public function testNotice(): void {
        $this->_modelLog->createNotice(
            $this->_logData['message'], $this->_logData['description'],
            $this->_logData['data'],
            $this->_logData['code'], $this->_logData['file'], $this->_logData['line'], $this->_logData['url']
        );
        
        $this->assertEquals($this->_modelLog->level, ModelLog::LEVEL_NOTICE);
        
        $this->_modelLog->createNotice(
            $this->_logData['message'], $this->_logData['description'],
            $this->_logData['data'],
            null, $this->_logData['file'], $this->_logData['line'], $this->_logData['url']
        );
        
        $this->assertEquals($this->_modelLog->level, ModelLog::LEVEL_NOTICE);
        $this->assertEquals($this->_modelLog->code, E_USER_NOTICE);
    }
    
    public function testRequest(): void {
        $modelLog = Factory::instance()->createModel('Log');
        
        $this->assertTrue($modelLog->setRequest($this->_request));
    }
    
    public function testDatabase(): void {
        $request = Factory::instance()->createRequest();
        
        $modelLogSave = Factory::instance()->createModel('Log');
        $modelLogSave->setRequest($request);
        
        $modelLogSave->create(
            $this->_logData['level'], $this->_logData['message'], $this->_logData['description'],
            $this->_logData['data'],
            $this->_logData['code'], $this->_logData['file'], $this->_logData['line'], $this->_logData['url']
        );
        
        $idSave = $modelLogSave->save();
        $this->assertTrue(boolval($idSave));
        
        $dataAfterSave = $modelLogSave->getDataAssoc();
        
        $modelLogGet = Factory::instance()->createModel('Log');
        $modelLogGet->setRequest($request);
        $modelLogGet->loadByPk($idSave);
        $dataAfterGet = $modelLogGet->getDataAssoc();
        
        $this->assertEquals($dataAfterSave, $dataAfterGet);
        
        $modelLogGet->message .= '!';
        $idGet = $modelLogGet->save();
        $dataAfterUpdated = $modelLogGet->getDataAssoc();
        
        $modelLogUpdatedGet = Factory::instance()->createModel('Log');
        $modelLogUpdatedGet->setRequest($request);
        $modelLogUpdatedGet->loadByPk($idGet);
        
        $dataAfterUpdatedGet = $modelLogUpdatedGet->getDataAssoc();
        
        $this->assertEquals($dataAfterUpdated, $dataAfterUpdatedGet);
    }
    
    private function getTestData(): array {
        return array(
            'level' => ModelLog::LEVEL_ERROR,
            'message' => 'Test message',
            'description' => 'Test description',
            'data' => array('test' => true),
            'code' => E_ERROR,
            'file' => 'log.php',
            'line' => 255,
            'url' => 'http://test.example.com/',
        );
    }
    
    public function testCostants(): void {
        $this->assertTrue(! empty(ModelLog::LEVEL_CRITICAL));
        $this->assertTrue(! empty(ModelLog::LEVEL_ERROR));
        $this->assertTrue(! empty(ModelLog::LEVEL_WARNING));
        $this->assertTrue(! empty(ModelLog::LEVEL_NOTICE));
    }
    
}
