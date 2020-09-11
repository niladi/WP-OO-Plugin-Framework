<?php

namespace WPPluginCoreTest\Unit\Persistence\DAO\Abstraction;

use WPPluginCore\Plugin;
use WPPluginCoreTest\Unit\TestHelper\TestCase;
use WPPluginCore\Persistence\DAO\Abstraction\DAO;

/**
 * Class DAOTest.
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 *
 * @covers \WPPluginCore\Persistence\DAO\Abstraction\DAO
 */
class DAOTest extends TestCase
{
    protected DAO $dAO;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->dAO = $this->getMockBuilder(DAO::class)
            ->setConstructorArgs([])
            ->getMockForAbstractClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->dAO);
    }

    public function testGetInstance(): void
    {
        self::assertSame($this->dAO::getInstance(), $this->dAO::getInstance());
        self::assertInstanceOf(DAO::class , $this->dAO::getInstance());
    }
}
