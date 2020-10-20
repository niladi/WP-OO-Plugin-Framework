<?php
namespace WPPluginCoreTest\Unit\TestHelper;

use Mockery;
use Brain\Monkey;
use WPPluginCore\Plugin;
use PHPUnit\Framework;
use WPPluginCore\Service\DBInit;
use WPPluginCoreTest\Unit\TestHelper\TestException;

if (isset($_ENV['DB_HOST'])) {
    define('DB_HOST', $_ENV['DB_HOST']);
} else {
    define('DB_HOST', 'localhost:3306');
}

define('ABSPATH', 1);

class TestCase extends Framework\TestCase {

    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public static array $activationHooks = array();

    private array $shortCodes = array();
    
    /**
     * {@inheritdoc}
     */
    protected function setUp() : void 
    {
        parent::setUp();
        Monkey\setUp();
        Monkey\Functions\stubs([
            '__' =>  function ($arg1, $arg2 = 'default') 
            {
                if ($arg2 === 'wp-licence-sales') {
                    return $arg1;
                }
                throw new TestException('Language function is called wrong');
                
            },
            "post_type_exists" => true,
            'get_terms' => function ($arg1, $arg2) 
            {
                if ($arg1 == 'product_cat') {
                    $arr = array();
                    for ($i = 0; $i < 9; $i++) {
                        $term = new \stdClass();
                        $term->term_id = $i;
                        $term->name = 'cat' . $i;
                        array_push($arr, $term);
                    }
                    return $arr;
                }
                return array();
            },
            'get_option' => function (string $option)
            {
                return "PLZ";
            },
            'register_activation_hook' => function (string $file, callable $callback  ) {
                static::$activationHooks[$file] = $callback ;
            },
            'home_url' => 'https://example.org',
            'add_shortcode' => function (string $shortcode, callable $callback) {
                $this->shortCodes[$shortcode] = $callback;
            },
            'plugins_url' => function($arg1, $arg2) {
                return $arg2;
            }
        ]);

        $mock = Mockery::namedMock('WP_REST_Server', ClassConstantWPRESTStub::class);

    }

    protected static function getActiviationHook(string $file) : ?callable
    {
        return static::$activationHooks[$file];
    }

    protected function getShortcode(string $shortcode) : ?callable
    {
        return $this->shortCodes[$shortcode];
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown() : void 
    {
        
        Monkey\tearDown();         //Necessery Mockery::clear() is inside Mokey\tearDown()
        parent::tearDown();
    }


    final static function getSubclasses(string $parentClass) : array
    {
        return array_filter(get_declared_classes(), fn($class) => is_subclass_of($class, $parentClass)); 
    }
}

class ClassConstantWPRESTStub {
    const READABLE = 'GET';
}

