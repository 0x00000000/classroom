<?php

declare(strict_types=1);

namespace Classroom\Module\Config;

/**
 * Stores configuration data for other modules.
 */
class ConfigCommon extends ConfigBase {
    
    /**
     * @var array $_data Stores configuration data.
     */
    protected $_data = array(
        'application' => array(
            'session_name' => 'sid',
            // Must be set if application is not in site's root. F e '/b2b/public'
            'urlPrefix' => '',
            // Must be set if application is not in site's root.
            // 'baseUrl' => 'http://example.com/b2b/public',
        ),
        'contentImage' => array(
            // Should be in public directory.
            'uploadPath' => '/public/media/images',
        ),
        'nicEdit' => array(
            'nicUploadPath' => '/public/media/nicUpload',
            'nicYoutubeWidth' => '560',
            'nicYoutubeHeight' => '315',
        ),
        'database' => array(
            'server' => 'localhost',
            'login' => 'mysql',
            'password' => 'localpass',
            'name' => 'classroom',
            'prefix' => 'classroom_',
        ),
        'site' => array(
            'caption' => 'Learn English',
            'title' => 'Learn English',
            'keywords' => '',
            'description' => '',
            'sessionLifeTime' => '86400',
            'redirectAfterLoginLifeTime' => '600',
        ),
        'user' => array(
            'salt1' => 'abcdefghij',
            'salt2' => 'klmnopqrst',
        ),
        'admin' => array(
            'mainPageUrl' => '/admin',
            'itemsPerPage' => 20,
        ),
        'teacher' => array(
            'mainPageUrl' => '/teacher',
            'itemsPerPage' => 20,
        ),
        'student' => array(
            'mainPageUrl' => '',
            'itemsPerPage' => 20,
        ),
    );
    
}
