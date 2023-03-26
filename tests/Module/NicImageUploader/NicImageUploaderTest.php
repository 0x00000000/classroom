<?php

declare(strict_types=1);

namespace ClassroomTest\Module\NicImageUploader;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;
use Classroom\System\FileSystem;
use ClassroomTest\UsesModelUserTrait;

include_once(dirname(__FILE__) . '/../../init.php');

final class NicImageUploaderTest extends TestCase {
    use UsesModelUserTrait;

    private $_request = null;

    private $_auth = null;

    public function setUp(): void {
        $this->_request = Factory::instance()->createRequest();

        $this->_auth = Factory::instance()->createAuth($this->_request);

        $this->_existingPassword = $this->getUniquePassword();
        $this->_existingUserModel = $this->createUser(
            $this->getUniqueLogin(),
            $this->_existingPassword
        );

        // I want be sure that we are not logged in.
        $this->_auth->logout();
    }

    public function testUploadFileExists(): void {
        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );

        $nicImageUploader = Factory::instance()->createModule('NicImageUploader');
        $nicImageUploader->setRequest($this->_request);
        $nicImageUploader->setAuth($this->_auth);

        // This file should exists.
        $imagePath = FileSystem::getRoot()
            . FileSystem::getDS() . 'public'
            . FileSystem::getDS() . 'images'
            . FileSystem::getDS() . 'greeking.jpg';

        $this->assertTrue(file_exists($imagePath));

        $fileInfo = [
            'name' => 'test.jpg',
            'tmp_name' => $imagePath,
            'type' => 'image/jpeg',
        ];
        $result = $nicImageUploader->upload($fileInfo);
        $this->assertEquals($result['status'], 200);
        $this->assertTrue($result['success']);
        $this->assertTrue(! empty($result['data']['id']));
        $this->assertTrue(! empty($result['data']['link']));
    }

    public function testUploadFileNotExists(): void {
        $this->_auth->login(
            $this->_existingUserModel->login,
            $this->_existingPassword
        );

        $nicImageUploader = Factory::instance()->createModule('NicImageUploader');
        $nicImageUploader->setRequest($this->_request);
        $nicImageUploader->setAuth($this->_auth);

        // This file should not exists.
        $imagePath = FileSystem::getRoot()
            . FileSystem::getDS() . 'public'
            . FileSystem::getDS() . 'images'
            . FileSystem::getDS() . 'greekingFineNotExists.jpg';

        $fileInfo = [
            'name' => 'test.jpg',
            'tmp_name' => $imagePath,
            'type' => 'image/jpeg',
        ];
        $result = $nicImageUploader->upload($fileInfo);

        $this->assertEquals($result['status'], 200);
        $this->assertFalse($result['success']);
        $this->assertTrue(! empty($result['error']));
    }

    public function testUploadNoAuth(): void {
        $this->_auth->logout();

        $nicImageUploader = Factory::instance()->createModule('NicImageUploader');
        $nicImageUploader->setRequest($this->_request);
        $nicImageUploader->setAuth($this->_auth);

        // This file should exists.
        $imagePath = FileSystem::getRoot()
            . FileSystem::getDS() . 'public'
            . FileSystem::getDS() . 'images'
            . FileSystem::getDS() . 'greeking.jpg';

        $this->assertTrue(file_exists($imagePath));

        $fileInfo = [
            'name' => 'test.jpg',
            'tmp_name' => $imagePath,
            'type' => 'image/jpeg',
        ];
        $result = $nicImageUploader->upload($fileInfo);
        $this->assertEquals($result['status'], 200);
        $this->assertFalse($result['success']);
        $this->assertTrue(! empty($result['error']));
    }
}
