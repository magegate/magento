<?php

require_once __DIR__ . '/../../../../bootstrap/autoload.php';

class MagegateMagentoTestCase extends Illuminate\Foundation\Testing\TestCase {

    public static function createApplication()
    {
        $unitTesting = true;
        $testEnvironment = 'testing';
        return require __DIR__ . '/../../../../bootstrap/start.php';
    }

}