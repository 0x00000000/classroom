<?php

declare(strict_types=1);

namespace classroom;

include_once('FactoryBase.php');

/**
 * Creates modules and models.
 */
class FactoryAdmin extends FactoryBase {
    
    /**
     * @var string $_moduleNamePostfix Postfix for some modules' names.
     */
    protected $_moduleNamePostfix = 'Admin';
    
    /**
     * Class constructor.
     */
    public function __construct() {
    }
    
}
