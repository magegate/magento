<?php
return array(
    /**
     * Magento root category id for host category==0
     */
    'id' => 2,

    /**
     * Model assignment mage member names to host member names
     */
    'arrange' => array(
        'category_id' => 'node',
        'parent_id' => 'parents',
        'default_sort_by' => 'sort_by',
    ),

    /**
     * Model default values for mage members
     */
    'default' => array(
        'default_sort_by' => 'position',
        'is_active' => 1,
        'include_in_menu' => 1,
    ),

    /**
     * Data type translations for mage members
     */
    'host2mage' => array(
        'default_sort_by' => array(
            'bestvalue' => 'position',
        ),
    ),
    'mage2host' => array(
        'default_sort_by' => array(
            'position' => 'bestvalue',
        ),
    ),
);