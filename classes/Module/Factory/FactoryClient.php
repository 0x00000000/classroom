<?php

declare(strict_types=1);

namespace Classroom\Module\Factory;

/**
 * Creates modules and models.
 */
class FactoryClient extends FactoryBase {
    
    /**
     * @var string $_moduleNamePostfix Postfix for some modules' names.
     */
    protected $_moduleNamePostfix = 'Client';
    
    /**
     * Class constructor.
     */
    public function __construct() {
    }
    
}
