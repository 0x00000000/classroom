<?php

declare(strict_types=1);

namespace classroom;

include_once('FactoryBase.php');

/**
 * Creates modules and models.
 */
class FactoryAjax extends FactoryBase {
    
    /**
     * @var string $_moduleNamePostfix Postfix for some modules' names.
     */
    protected $_moduleNamePostfix = 'Ajax';
    
    /**
     * Class constructor.
     */
    public function __construct() {
    }
    
}
