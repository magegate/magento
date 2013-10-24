<?php

class MagegateMagentoTestCaseModels extends MagegateMagentoTestCase {

    protected $modelClassName = 'Missing_Model_Class_Name';

    public function assertModelExists($classname)
    {
        $this->assertTrue(is_a($model = $this->getModel(),$classname),
            "$classname model cannot de created");
        $this->assertTrue(Schema::hasTable($t=$model->getTable()),
            "Schema table '$t' for model $classname does not exists");
        return $model;
    }

    public function getModel()
    {
        return new $this->modelClassName();
    }

    public function testModel()
    {
        $this->assertModelExists($this->modelClassName);
    }


}