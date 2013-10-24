<?php
return array(
    /**
     * Model assignment mage member names to host member names
     */
        'arrange' => array(
        'sku' => 'ASIN',
        'product_id' => 'ASIN',
        'price' => 'amount',
        'name' => 'title',
    ),

    /**
     * Model default values for mage members
     */
    'default' => array(
        'set' => 4,
        'type' => 'simple',
        'weight' => 0.0000,
        'visibility' => 4,
        'status' => 1,
        'tax_class_id' => 0
    ),

    /**
     * Data type translations for mage members
     */
    'host2mage' => array(
        'status' => array(
            'enabled' => 1,
            'disabled' => 2,
        ),
        'tax_class_id' => array(
            'None' => 0,
            'Taxable_Goods' => 2,
            'Shipping' => 4,
        ),
    ),
    'mage2host' => array(
        'status' => array(
            1 => 'enabled',
            2 => 'disabled',
        ),
        'tax_class_id' => array(
            0 => 'None',
            2 => 'Taxable_Goods',
            4 => 'Shipping',
        ),
    ),

);