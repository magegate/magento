<?php

return array(
    /**
     * Mock Magento Api classes with some stub classes for testing purposes only
     */
    'magento' => array(
//        'catalog/category' => '\Magegate\Magento\Stubs\CatalogCategoryStub',
//        'catalog/product' => '\Magegate\Magento\Stubs\CatalogProductStub',
    ),

    /**
     * Entity Model host configurations
     */
    'entity' => array(
        'catalog/category' => array(
            'testing' => include __DIR__ . '/entity/catalog/category/testing.php'
        ),
        'catalog/product' => array(
            'testing' => include __DIR__ . '/entity/catalog/product/testing.php'
        )
    ),
);