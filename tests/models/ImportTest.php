<?php

class ImportTest extends MagegateMagentoTestCaseModels {

    protected $modelClassName = '\Magegate\Import';
    protected $host;

    public function setUp()
    {
        parent::setUp();
        $this->host = \App::environment();
    }

}