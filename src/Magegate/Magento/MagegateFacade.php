<?php

namespace Magegate\Magento;

use Illuminate\Support\Facades\Facade;

class MagegateFacade extends Facade{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'Mage'; }

}