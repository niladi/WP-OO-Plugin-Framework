<?php

namespace WPPluginCoreTest\Unit\Domain\Entity\Abstraction;

use ReflectionClass;
use WPPluginCoreTest\Unit\TestHelper\TestCase;
use WPPluginCore\Exception\IllegalKeyException;
use WPPluginCore\Exception\IllegalArgumentException;
use WPPluginCore\Domain\Entity\Abstraction\Entity;
use WPPluginCore\Domain\Entity\Attribute\Implementation\Text;
use WPPluginCore\Domain\Entity\Attribute\Implementation\EntityID;

/**
 * Class EntityTest.
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 *
 * @covers \WPPluginCore\Domain\Entity\Abstraction\Entity
 */
class EntityTest extends TestCase
{
    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->attributes = [];
        $this->entity = \Mockery::mock(Entity::class);
        $this->entity->shouldAllowMockingProtectedMethods();
        $this->entity->makePartial();
        $this->entity->shouldReceive(['getTable' => 'table_name', 'getIDIntType' => 'INT']);
        /* $this->entity = $this->getMockBuilder(Entity::class)
            ->setConstructorArgs([$this->attributes])
            ->setMethods()
            ->getMockForAbstractClass();*/
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->entity);
        unset($this->attributes);
    }

    /**
     * Tests if the function getTable exists and returns the correct string
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testGetTable(): void
    {
        self::assertEquals($this->entity->getTable(),'table_name');
    }

    /**
     * Checks if the function avlidate exists and returns an entity for methot chaining
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testValidate(): void
    {
        self::assertEquals($this->entity, $this->entity->validate());
    }

    /**
     * Tests the addAttributes function
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testAddAttributes(): void
    {
        self::assertFalse($this->entity->hasAttribute(Entity::KEY_ID));
        $this->entity->addAttributes();
        self::assertTrue($this->entity->hasAttribute(Entity::KEY_ID));
    }

    /**
     * Tests the method __get (with the default values)
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function test__get(): void
    {
        $this->entity->addAttributes(); // because constructor is not called
        self::assertEquals($this->entity->__get(Entity::KEY_ID), new EntityID(Entity::KEY_ID, 'ID', $this->entity::getIDIntType()));
        self::assertNull($this->entity->__get('wrong_key'));
    }

    /**
     * Tests the method get
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testGet(): void
    {
        $this->entity->addAttributes(); // because constructor is not called
        self::assertEquals($this->entity->get(Entity::KEY_ID), new EntityID(Entity::KEY_ID, 'ID', $this->entity::getIDIntType()));
        self::assertNull($this->entity->get('wrong_key'));
    }

    /**
     * Tests the methot if the default ID is returned
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testGetID(): void
    {
        $this->entity->addAttributes(); // because constructor is not called
        self::assertEquals($this->entity->getID(), -1);
    }

    /**
     * Tests if a correct id is settet write
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testSetID(): void
    {
        $this->entity->addAttributes(); // because constructor is not called
        self::assertEquals($this->entity->getID(), -1);
        $this->entity->setID(1);
        self::assertEquals($this->entity->getID(), 1);
        $this->entity->setID('2');
        self::assertEquals($this->entity->getID(), 2);
    }

    /**
     * Test try to set a wrong id
     *
     * @return boid
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testSetWrongID() : void
    {
        $this->entity->addAttributes();
        $this->expectException(IllegalArgumentException::class);
        $this->entity->setID(-2);
    }

    /**
     * Tests the getAttributes methot (with Refelction [autogen] und default mock)
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testGetAttributes(): void
    {
        $expected = [];
        $property = (new ReflectionClass(Entity::class))
            ->getProperty('attributes');
        $property->setAccessible(true);
        $property->setValue($this->entity, $expected);
        self::assertSame($expected, $this->entity->getAttributes());

        $this->entity->addAttributes();
        self::assertEquals(array(Entity::KEY_ID => new EntityID(Entity::KEY_ID, 'ID', $this->entity::getIDIntType() )), 
        $this->entity->getAttributes());
    }

    /**
     * Tests the hasAttributes function
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testHasAttribute(): void
    {
        self::assertFalse($this->entity->hasAttribute(Entity::KEY_ID));
        $this->entity->addAttributes();
        self::assertTrue($this->entity->hasAttribute(Entity::KEY_ID));
        self::assertFalse($this->entity->hasAttribute('wrong_id'));
    }

    /**
     * Tests the getAttributesKeys function
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testGetAttributesKeys(): void
    {
        self::assertEmpty($this->entity->getAttributesKeys(false));
        $this->entity->addAttributes();
        self::assertEquals([Entity::KEY_ID], $this->entity->getAttributesKeys(false));
        self::assertEmpty($this->entity->getAttributesKeys());
    }

    /**
     * Tests the getAttribute function with the correct key
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testGetAttribute(): void
    {
        $this->entity->addAttributes(); // because constructor is not called
        self::assertEquals($this->entity->getAttribute(Entity::KEY_ID), new EntityID(Entity::KEY_ID, 'ID', $this->entity::getIDIntType()));
        $this->expectException(IllegalKeyException::class);
        $this->entity->getAttribute('wrong_key');
    }

    /**
     * Testst the getAttributeValueSerializied function
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testGetAttributeValueForDB(): void
    {
        $attribute = new Text('text_key', 'my_text');
        $attribute->setValue('my text');
        $this->entity->addAttribute($attribute);
        self::assertSame($attribute->getValueForDB(), 
            $this->entity->getAttributeValueForDB('text_key'));
        $this->expectException(IllegalKeyException::class);
        $this->entity->getAttributeValueForDB('wrong_key');
    }

    /**
     * tests the getAttributeValue function
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testGetAttributeValue(): void
    {
        $attribute = new Text('text_key', 'my_text');
        $attribute->setValue('my text');
        $this->entity->addAttribute($attribute);
        self::assertSame($attribute->getValue(), 
            $this->entity->getAttributeValue('text_key'));
        $this->expectException(IllegalKeyException::class);
        $this->entity->getAttributeValueForDB('wrong_key');
    }

    /**
     * tests setAttribuetValue
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testSetAttributeValue(): void
    {
        $attribute = new Text('text_key', 'my_text');
        $this->entity->addAttribute($attribute);
        $this->entity->setAttributeValue('text_key', 'my value');
        self::assertSame($attribute->getValue(), 
            $this->entity->getAttributeValue('text_key'));
        self::assertEquals($this->entity->getAttributeValue('text_key'), 'my value');
        $this->expectException(IllegalKeyException::class);
        $this->entity->setAttributeValue('wrong_key', 'my value');
    }

    /**
     * Tests if the attribute values are returened right as for DB and standort as assossiative array
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testGetAttributesValuesAssoc(): void
    {
        $attribute = new Text('text_key', 'my_text');
        $attribute->setValue('my value');
        $this->entity->addAttribute($attribute);

        $arr = $this->entity->getAttributesValuesAssoc(true);
        self::assertNotNull($arr);
        self::assertEquals(array(
            'text_key' => '\'my value\''
        ), $arr);

        $arr = $this->entity->getAttributesValuesAssoc(false);
        self::assertNotNull($arr);
        self::assertEquals(array(
            'text_key' => 'my value'
        ), $arr);
    }

    public function testAttributesForDB(): void
    {
        $attribute = new Text('text_key', 'my_text');
        $attribute->setValue('my value');
        $this->entity->addAttribute($attribute);
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();


    }

    public function testBeforeSave(): void
    {
        self::assertNull($this->entity->beforeSave());
    }

    public function testAfterSave(): void
    {
        self::assertNull($this->entity->afterSave());
    }

    public function testGetPrimaryKeysSerialized(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetForeignKeys(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testConnectKeys(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    /**
     * Tests if the placeholder exists and is empty
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    public function testPlaceholder(): void
    {
        self::assertNull($this->entity->placeholder());
    }
}
