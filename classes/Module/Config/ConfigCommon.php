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
            // Must be set if application is not in site's root.
            // 'baseUrl' => 'http://example.com',
        ),
        'database' => array(
            'server' => 'localhost',
            'login' => 'mysql',
            'password' => '',
            'name' => 'classroom',
            'prefix' => 'classroom_',
        ),
        'site' => array(
            'caption' => 'Learn English',
            'title' => 'Learn English',
            'keywords' => '',
            'description' => '',
        ),
        'user' => array(
            'salt1' => 'DKfoA.d,XO',
            'salt2' => 'cqPdMs!oee',
        ),
        'admin' => array(
            'itemsPerPage' => 20,
        ),
        'teacher' => array(
            'itemsPerPage' => 20,
        ),
        'student' => array(
            'itemsPerPage' => 20,
        ),
    );
    
}
