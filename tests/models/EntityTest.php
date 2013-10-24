<?php

class EntityTest extends MagegateMagentoTestCaseModels {

    protected $modelClassName = '\Magegate\Entity';
    protected $host;

    public function setUp()
    {
        parent::setUp();
        $this->host = \App::environment();
    }

    public function testConfig()
    {
        $this->assertNotEmpty($api=\Config::get($k='magento::config.entity.api'),
            "Config $k not found");
    }

}