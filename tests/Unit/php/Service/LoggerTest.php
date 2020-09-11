<?php

namespace WPPluginCoreTest\Unit\Service;

use Mockery;
use ReflectionClass;
use WPPluginCore\Plugin;
use Logger as LoggerAlias;
use WPPluginCore\Logger;
use function PHPUnit\Framework\assertNull;

use WPPluginCoreTest\Unit\TestHelper\TestCase;

/**
 * Class LoggerTest.
 *
 * @author Niklas Lakner niklas.lakner@gmail.com
 *
 * @covers \WPPluginCore\Logger
 */
class LoggerTest extends TestCase
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass() : void
    {
        if (!defined(Plugin::KEY_FILE)) {
            define(Plugin::KEY_FILE, __FILE__);
        }

        if (!Logger::isRegistered()) {
            Logger::registerMe();
        }
        
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetInstance(): void
    {
        self::assertInstanceOf(Logger::class, Logger::getInstance());
    }

    public function testError(): void
    {
        assertNull(Logger::error('test error'));
    }

    public function testInfo(): void
    {
        assertNull(Logger::info('test info'));
    }
}
