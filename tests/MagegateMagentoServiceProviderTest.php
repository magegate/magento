<?php

class MagegateMagentoServiceProviderTest extends MagegateMagentoTestCase {

    public function testMagentoServiceProvider()
    {
        $mage = \App::make('Mage');
        $this->assertTrue(is_object($mage),
            'Magegate Mage create failed');

        $model = $mage->getModel('core/website');
        $this->assertTrue(is_object($model),
            "Magento Mage get model core/website failed");
        $this->assertTrue(is_a($model,$c='Mage_Core_Model_Website'),
            "Magento Mage get model core/website is not a $c");
    }

}