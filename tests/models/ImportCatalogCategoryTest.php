<?php

use \Magegate\Import;
use \Magegate\ImportCatalogCategory;
use \Magegate\EntityCatalogCategory;

class ImportCatalogCategoryTest extends ImportTest {

    public function testModel()
    {
        parent::testModel();
    }

    /**
     * @depends testModel
     */
    public function testCreate()
    {
        $entityCatalogCategoryTest = new EntityCatalogCategoryTest();
        $entityCatalogCategoryTest->setUp();
        $entityCatalogCategoryTest->testModel();
        $entityCatalogCategoryTest->testConfig();

        $list = $entityCatalogCategoryTest->testSortListPathHostIdList();
        $host = $this->host;

        $this->assertNotEmpty($import=ImportCatalogCategory::create(array(
            'host' => $host,
            'list' => $list,
        )),"Create Import failed");

        while($import->queueNext())
        {
            $this->assertTrue($import->queueWork(),
                "Queued catalog/category work failed");
        }

        $this->assertEquals('done',$import->status,
            "Queued catalog/category not done");

        $this->assertEquals($import->queued,$import->finish+$import->failed,
            "Queued catalog/category entities incomplete");

        $this->assertEquals($import->number,$import->queued,
            "Import catalog/category entities incomplete");

        $this->assertTrue($import->delete(),
            "Deleting Import failed");
    }

    /**
     * @depends testCreate
     */
    public function testDelete()
    {
        EntityCatalogCategory::helperDeleteChildren($this->host,0);
    }
}