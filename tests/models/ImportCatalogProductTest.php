<?php

use \Magegate\Import;
use \Magegate\ImportCatalogProduct;
use \Magegate\EntityCatalogProduct;

class ImportCatalogProductTest extends ImportTest {

    public function testModel()
    {
        parent::testModel();
    }

    /**
     * @depends testModel
     */
    public function testCreate()
    {
        $entityCatalogProductTest = new EntityCatalogProductTest();
        $entityCatalogProductTest->setUp();
        $entityCatalogProductTest->testModel();
        $entityCatalogProductTest->testConfig();

        $list = $entityCatalogProductTest->testHost2MageList();
        $host = $this->host;

        $this->assertNotEmpty($import=ImportCatalogProduct::create(array(
            'host' => $host,
            'list' => $list,
        )),"Create Import failed");

        while($import->queueNext())
        {
            $this->assertTrue($import->queueWork(),
                "Queued catalog/product work failed");
        }

        $this->assertEquals('done',$import->status,
            "Queued catalog/product not done");

        $this->assertEquals($import->queued,$import->finish+$import->failed,
            "Queued catalog/product entities incomplete");

        $this->assertEquals($import->number,$import->queued,
            "Import catalog/product entities incomplete");

        $this->assertTrue($import->delete(),
            "Deleting Import failed");
    }

    public function testDelete()
    {
        EntityCatalogProduct::helperDeleteAll($this->host);
    }
}