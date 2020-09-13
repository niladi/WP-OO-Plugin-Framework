<?php


namespace WPPluginCore\Web\Abstraction;

use Closure;
use WP_REST_Request;
use ReflectionFunction;
use WPPluginCore\Plugin;
use WPPluginCore\Exception\IllegalArgumentException;

defined('ABSPATH') || exit;

abstract class API extends Endpoint
{
    private const KEY_ROUTE = 'route';
    private const KEY_ARGS = 'args';
    private const KEY_METHOD = 'methods';
    private const KEY_CALLBACK = 'callback';
    private const KEY_PERMISSION_CALLBACK = 'permission_callback';
    public const KEY_AUTH_HEADER = "authorization";
    
    private const NAMEPSACE = 'ls/v1';

    protected array $endpoints = array();

    protected abstract static function getNamespace() : string;


    /**
     * @inheritDoc
     */
    static public function registerMe(Plugin $plugin): void 
    {
        parent::registerMe($plugin);
        add_action('rest_api_init', array(static::getInstance(), 'registerEndpoints'));
    }

    /**
     * Register the rest route for each enpoints
     *
     * @return void
     * @author Niklas Lakner niklas.lakner@gmail.com
     */
    final public function registerEndpoints() : void
    {
        foreach ($this->endpoints as $endpoint) {
            register_rest_route(self::NAMEPSACE, $endpoint[self::KEY_ROUTE], $endpoint[self::KEY_ARGS]);
        }
    }

    /**
     * Adds the whole endpoints
     */
    abstract protected function addEndpoints() : void ;

    /**
     * Add an Wordpress Rest Route
     *
     * @param string $namespace_path the main namespace for the endpoint (should be unique over the full rest API)
     * @param string $param_path the route (after the namespace)
     * @param callable $callback the function which should get after calling the route (should have the return type WP_REST_Response)
     * @param string $method the HTTP Verb (default is GET)
     * 
     * @throws IllegalArgumentException if the namepace path is already in use
     */
    final protected function addEndpoint(string $namespace_path,string $param_path, callable $callback, string $method=\WP_REST_Server::READABLE)
    {
        if (key_exists( $namespace_path, $this->endpoints)) {
            throw new IllegalArgumentException('The namespace_path ' . $namespace_path . ' is already an key');
        } 
        $this->endpoints[$namespace_path] = array(
            self::KEY_ROUTE => static::getNamespace() . '/' . $namespace_path . '/'. $param_path,
            self::KEY_ARGS => array(
                self::KEY_METHOD => $method,
                self::KEY_CALLBACK => $callback,
                self::KEY_PERMISSION_CALLBACK =>array($this, 'permission'),
            )
        );
    }

    /**
     * The default permission function
     *
     * @param WP_REST_Request $request the request which should get permitted
     *
     * @return bool true
     */
    public function permission(WP_REST_Request $request)
    {
        return true;
    }

    protected function __construct()
    {
        parent::__construct();
        $this->endpoints = array();
        $this->addEndpoints();
    }
}
