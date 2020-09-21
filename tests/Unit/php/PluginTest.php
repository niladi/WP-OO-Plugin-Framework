<?php

namespace WPPluginCoreTest\Unit;

use Mockery;
use WPPluginCore\Plugin;

use WPPluginCoreTest\Unit\TestHelper\TestCase;
use WPPluginCore\Service\Wordpress\Entity\Save;
use WPPluginCore\Service\Wordpress\Entity\Metabox;
use WPPluginCore\Service\Wordpress\Entity\PostTypeRegistration;
use WPPluginCore\Util\Date as ImplementationDate;

/**
 * Class AppTest.
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 *
 * @covers \WPPluginCore\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * @var Plugin
     */
    protected $app;

    /**
     * @var string
     */
    protected $file;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->file = __FILE__;
        $this->app = new Plugin($this->file, $this->file, 'wp-test', array(), array());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->app);
        unset($this->file);
    }

    public function testRun(): void
    {      

        $classes = array(
            Metabox::class,
            Save::class,
            PostTypeRegistration::class,
            ImplementationDate::class
        );
        
        foreach ($classes as $class) {
            self::assertFalse($class::isRegistered(), 'This class should be not registerd : ' . $class);
        }
        
        $this->app->run();

        foreach ($classes as $class) {
            self::assertTrue($class::isRegistered(),'This class should be registered : ' . $class );
        }
    }
}
