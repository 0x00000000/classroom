<?php

declare(strict_types=1);

namespace ClassroomTest\Module\Config;

use Classroom\Module\Config\Config;
use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

include_once(dirname(__FILE__) . '/../../init.php');

final class ConfigTest extends TestCase {
    
    public function testAdminConfig(): void {
        $config = Factory::instance()->createConfig();
        $section = 'admin';
        $keysList = ['mainPageUrl', 'itemsPerPage'];
        $this->checkConfigSection($config, $section, $keysList);
    }
    
    public function testApplicationConfig(): void {
        $config = Factory::instance()->createConfig();
        $section = 'application';
        $keysList = ['session_name'];
        $this->checkConfigSection($config, $section, $keysList);
    }

    public function testContentImageConfig(): void {
        $config = Factory::instance()->createConfig();
        $section = 'contentImage';
        $keysList = ['uploadPath'];
        $this->checkConfigSection($config, $section, $keysList);
    }

    public function testDatabaseConfig(): void {
        $config = Factory::instance()->createConfig();
        $section = 'database';
        $keysList = ['server', 'login', 'password', 'name', 'prefix'];
        $this->checkConfigSection($config, $section, $keysList);
    }

    public function testNicEditConfig(): void {
        $config = Factory::instance()->createConfig();
        $section = 'nicEdit';
        $keysList = ['nicUploadPath', 'nicYoutubeWidth', 'nicYoutubeHeight'];
        $this->checkConfigSection($config, $section, $keysList);
    }

    public function testSiteConfig(): void {
        $config = Factory::instance()->createConfig();
        $section = 'site';
        $keysList = ['caption', 'title', 'sessionLifeTime', 'redirectAfterLoginLifeTime'];
        $this->checkConfigSection($config, $section, $keysList);
    }

    public function testStudentConfig(): void {
        $config = Factory::instance()->createConfig();
        $section = 'student';
        $keysList = ['mainPageUrl', 'itemsPerPage'];
        $this->checkConfigSection($config, $section, $keysList);
    }

    public function testTeacherConfig(): void {
        $config = Factory::instance()->createConfig();
        $section = 'teacher';
        $keysList = ['mainPageUrl', 'itemsPerPage'];
        $this->checkConfigSection($config, $section, $keysList);
    }

    public function testUserConfig(): void {
        $config = Factory::instance()->createConfig();
        $section = 'user';
        $keysList = ['salt1', 'salt2'];
        $this->checkConfigSection($config, $section, $keysList);
    }

    private function checkConfigSection(Config $config, string $section, array $keysList) {
        $this->assertTrue(is_array($config->get($section)));
        $this->assertTrue(count($config->get($section)) > 0);
        foreach ($keysList as $key) {
            $this->assertTrue(! is_null($config->get($section, $key)));
        }
    }
}
