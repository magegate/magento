<?php

use Magegate\ImportCatalogCategory;

class ImportCatalogCategoryControllerTest extends MagegateMagentoTestCase {

    public function setUp()
    {
        parent::setUp();
        $this->host = \App::environment();
    }

    public function testShow()
    {
        $this->assertNotEmpty($import=ImportCatalogCategory::create(array(
            'host' => $host = $this->host,
            'list' => array(),
        )),"Create Import failed");

        $this->assertNotEquals(0,$id=$import->id,
            "Import id is Zero");

        $response = $this->call('GET',$url="/api/magegate/imports/$host/catalog/category/$id");
        $this->assertTrue(is_a($response,'\Illuminate\Http\Response'),
            "Invalid response from $url");
        $this->assertEquals(200,$code=$response->getStatusCode(),
            "Response status code $code != 200 from $url");

        $this->assertTrue(is_object($object = json_decode($response->getContent())),
            "Response is not a json object");

        $import->forceDelete();
    }

}