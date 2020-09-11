<?php

namespace WPPluginCoreTest\Unit\Util;

use WPPluginCore\Util\Parser;
use WPPluginCoreTest\Unit\TestHelper\TestCase;

/**
 * Class ParserTest.
 *
 * @covers \WPPluginCore\Util\Parser
 */
class ParserTest extends TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->parser = new Parser();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->parser);
    }

    public function testStrToInt(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetAuthString(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
