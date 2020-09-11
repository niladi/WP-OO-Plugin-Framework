<?php
namespace WPPluginCoreTest\Unit\TestHelper;


use WPPluginCore\DBInit;
use WPPluginCoreTest\Unit\TestHelper\TestCase;


class TestCaseWithDB extends TestCase {
    
    private DBInit $initDB;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void 
    {
        parent::setUp();
        $this->initDB = new DBInit();
        $this->initDB->initDB();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown() : void 
    {
        if (isset($this->initDB)) {
            $this->initDB->dropDB();
            unset($this->initDB);
        }

        parent::tearDown();
    }

}
