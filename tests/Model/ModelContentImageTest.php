<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

use Classroom\System\FileSystem;
use Classroom\Module\Factory\Factory;
use Classroom\Module\Request\Request;
use Classroom\Model\ModelContentImage;

final class ModelContentImageTest extends ModelDatabase {
    protected string $_modelName = 'ContentImage';

    private ?ModelContentImage $_contentImage;

    private ?Request $_request;

    private const FILE_NOT_EXISTS = 'path_not_exists.jpg';

    public function setUp(): void {
        $this->_request = Factory::instance()->createRequest();
        $this->_contentImage = Factory::instance()->createModel($this->_modelName);
        $this->_contentImage->setRootUrl($this->_request->getRootUrl());
    }

    public function testSetAndGetFileInfo(): void {
        $fileInfo = $this->getFileInfo();
        $this->assertTrue(file_exists($fileInfo['tmp_name']));

        $result = $this->_contentImage->setFileInfo($fileInfo);
        $this->assertTrue($result);
        $gotFileInfo = $this->_contentImage->getFileInfo();
        $this->assertEquals($gotFileInfo, $fileInfo);

        $fileInfoIncorrect = $this->getFileInfo();
        $fileInfoIncorrect['name'] .= '_new';
        $fileInfoIncorrect['type'] .= '_new';
        $fileInfoIncorrect['tmp_name'] = self::FILE_NOT_EXISTS;
        $result = $this->_contentImage->setFileInfo($fileInfoIncorrect);
        $this->assertFalse($result);
        $gotFileInfo = $this->_contentImage->getFileInfo();
        $this->assertEquals($gotFileInfo, $fileInfo);

        $fileInfoIncorrect = $this->getFileInfo();
        $fileInfoIncorrect['name'] .= '_new';
        unset($fileInfoIncorrect['type']);
        $result = $this->_contentImage->setFileInfo($fileInfoIncorrect);
        $this->assertFalse($result);
        $gotFileInfo = $this->_contentImage->getFileInfo();
        $this->assertEquals($gotFileInfo, $fileInfo);

        $fileInfoIncorrect = $this->getFileInfo();
        unset($fileInfoIncorrect['name']);
        $fileInfoIncorrect['type'] .= '_new';
        $result = $this->_contentImage->setFileInfo($fileInfoIncorrect);
        $this->assertFalse($result);
        $gotFileInfo = $this->_contentImage->getFileInfo();
        $this->assertEquals($gotFileInfo, $fileInfo);
    }

    public function testSaveWithFile(): void {
        $fileInfo = $this->getFileInfo();
        $this->_contentImage->userId = '10';
        $this->_contentImage->setFileInfo($fileInfo);
        $result = $this->_contentImage->save();
        $this->assertTrue(! empty($result));
        $this->assertEquals($this->_contentImage->id, $result);
        $this->assertTrue(! empty($this->_contentImage->updated));
        $this->assertEquals($this->_contentImage->type, $fileInfo['type']);
        $this->assertTrue(! empty($this->_contentImage->size));
        $this->assertTrue(! empty($this->_contentImage->getUri()));
    }

    public function testSaveWithoutFile(): void {
        $this->_contentImage->userId = '10';
        $result = $this->_contentImage->save();
        $this->assertTrue(! empty($result));
        $this->assertEquals($this->_contentImage->id, $result);
        $this->assertNull($this->_contentImage->created);
        $this->assertNull($this->_contentImage->updated);
        $this->assertNull($this->_contentImage->filepath);
        $this->assertNull($this->_contentImage->disabled);
        $this->assertNull($this->_contentImage->deleted);
    }

    public function testSetRootUrlAndGetUri(): void {
        $rootUrl = 'http://another-url-example.com';
        $expencion = '.jpg';
        $this->_contentImage->setRootUrl($rootUrl);
        $this->_contentImage->save();
        $this->assertNull($this->_contentImage->getUri());

        $fileInfo = $this->getFileInfo();
        $this->_contentImage->setFileInfo($fileInfo);
        $this->_contentImage->save();
        $imageUri = $this->_contentImage->getUri();
        $this->assertTrue(! empty($imageUri));
        $this->assertTrue(strpos($imageUri, $rootUrl) !== false);
        $this->assertTrue(strpos($imageUri, $expencion) !== false);
    }

    public function testSetAndGetUploadPath(): void {
        $uploadPath = '/tmp';
        $this->_contentImage->setUploadPath($uploadPath);
        $gotPath = $this->_contentImage->getUploadPath();
        $this->assertEquals($uploadPath, $gotPath);
    }

    protected function getTestData(): array {
        return [
            [
                'userId' => '11',
                'filepath' => '/tmp/test1.jpg',
                'size' => '1024',
                'type' => 'image/jpeg',
                'width' => '112',
                'height' => '113',
                'created' => '2023-03-26 20:20:14',
                'updated' => '2023-03-26 20:20:15',
                'disabled' => '0',
                'deleted' => '0',
            ],
            [
                'userId' => '21',
                'filepath' => '/tmp/test2.jpg',
                'size' => '2048',
                'type' => 'image/png',
                'width' => '122',
                'height' => '123',
                'created' => '2023-03-26 20:20:24',
                'updated' => '2023-03-26 20:20:25',
                'disabled' => '1',
                'deleted' => '1',
            ],
        ];
    }

    private function getFileInfo() {
        $imagePath = FileSystem::getRoot()
            . FileSystem::getDS() . 'public'
            . FileSystem::getDS() . 'images'
            . FileSystem::getDS() . 'greeking.jpg';
        return [
            'name' => 'test.jpg',
            'tmp_name' => $imagePath,
            'type' => 'image/jpeg',
        ];

    }
}
