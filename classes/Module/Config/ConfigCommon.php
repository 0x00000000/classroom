<?php

declare(strict_types=1);

namespace classroom;

include_once('ConfigBase.php');

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
        ),
        'database' => array(
            'server' => 'localhost',
            'login' => 'root',
            'password' => '',
            'name' => 'classroom',
            'prefix' => 'classroom_',
        ),
    );
    
}
