<?php

namespace WPPluginCoreTest\Unit;

use WPPluginCore\Plugin;
use WPPluginCore\DBInit;
use WPPluginCoreTest\Unit\TestHelper\TestCase;
use WPPluginCore\Exception\QueryException;

/**
 * Class DBInitTest.
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 *
 * @covers \WPPluginCore\Service\DBInit
 */
class DBInitTest extends TestCase
{
    /**
     * @var DBInit
     */
    protected DBInit $dBInit;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->dBInit = DBInit::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        if ($this->dBInit->isInitialized()) {
            $this->dBInit->dropDB();
        }
        parent::tearDown();
        unset($this->dBInit);
    }



    /**
     * Test to drop db without any errors (important for testing)
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testDropDB(): void
    {
        $this->dBInit->initDB();
        self::assertTrue($this->dBInit->isInitialized());
        self::assertTrue($this->dBInit->dropDB());
        self::assertFalse($this->dBInit->isInitialized());
    }


    /**
     * Test to drop db without any errors (important for testing)
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testInitDB(): void
    {
        $this->dBInit->initDB();
        self::assertTrue($this->dBInit->isInitialized());
    }

    /**
     * Tests the register me function if dBInit is called correctly
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testRegisterMe(): void
    {
        if (!defined(Plugin::KEY_FILE)) {
            define(Plugin::KEY_FILE, 'test_filename');
        }

        if (!DBInit::isRegistered()) {
            DBInit::registerMe();
        }
        
        self::assertNotNull($this->getActiviationHook(constant(Plugin::KEY_FILE)));
        self::assertTrue(DBInit::isRegistered());
    }

    /**
     * Should returned false evry time
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testOnInit(): void
    {
        self::assertFalse($this->dBInit->onInit());
    }

    /**
     * check if the isInit value i setted correctly every time
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testIsInitialized() : void 
    {
        self::assertFalse($this->dBInit->isInitialized());
        $this->dBInit->initDB();
        self::assertTrue($this->dBInit->isInitialized());
        $this->dBInit->dropDB();
        self::assertFalse($this->dBInit->isInitialized());
    }
}
