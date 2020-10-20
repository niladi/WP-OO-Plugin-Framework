<?php

namespace WPPluginCore\Unit\Unit\Persistence\DAO\Entity\Abstraction;

use Mockery;
use Mockery\Mock;
use Psr\Log\LoggerInterface;
use WPPluginCore\Persistence\DB\DBConnector;
use WPPluginCoreTest\Unit\TestHelper\TestCase;
use WPPluginCore\Persistence\DAO\Entity\Abstraction\Entity;
use WPPluginCore\Domain\Entity\Abstraction\Entity as EntityDomain;
use WPPluginCore\Domain\Entity\Attribute\Implementation\Text;
use WPPluginCore\Persistence\DB\DBInit;

/**
 * Class EntityTest.
 *
 * @author
 * @author Niklas Lakner niklas.lakner@gmail.com
 *
 * @covers \WPPluginCore\Persistence\DAO\Entity\Abstraction\Entity
 */
class EntityTest extends TestCase
{
    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var DBConnector|Mock
     */
    protected $dbConnector;

    /**
     * @var LoggerInterface|Mock
     */
    protected $logger;

    /**
     * $example entity domain elemnt
     *
     * @var EntityDomain|Mock
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    protected $domain;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->domain = Mockery::mock(EntityDomain::class);
        $this->domain->shouldAllowMockingProtectedMethods();
        $this->domain->makePartial();
        $this->domain->shouldReceive(['getTable' => 'table_name', 'getIDIntType' => 'INT']);
        $this->domain->addAttribute(new Text('text', 'text'));
        $this->domain->setAttributeValue('text', 'my text');

        $this->entityClass = get_class($this->domain);
        $this->dbConnector = Mockery::mock(DBConnector::class);
        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->entity = $this->getMockBuilder(Entity::class)
            ->setConstructorArgs([$this->entityClass, $this->dbConnector, $this->logger])
            ->getMockForAbstractClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->entity);
        unset($this->entityClass);
        unset($this->dbConnector);
        unset($this->logger);
    }

    public function testCreate(): void
    {
        $this->entity->create($this->domain);
    }

    public function testCreateByArray(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testRead(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testReadSingleByEntityKeys(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testReadSingleByArray(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testReadSingleByKeyValue(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testReadMultipleByEntityKeys(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testReadMultipleByArray(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testReadMultipleByKeyValue(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testUpdate(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testDelete(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testQuerySingle(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
