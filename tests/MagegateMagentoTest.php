<?php

class MagegateMagentoTest extends MagegateMagentoTestCase {

    public function testConfig()
    {
        $this->assertTrue(is_string($basedir=\Config::get($c='magento::config.basedir',null)),
            "Config $c missing");
        $this->assertFileExists($basedir,
            "Config $c realpath not found");
    }

}