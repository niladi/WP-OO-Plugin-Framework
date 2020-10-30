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
    private string $namespace;


    public function __construct(string $namespace)
    {
        $this->endpoints = array();
        $this->namespace = $namespace;
        $this->addEndpoints();
    }

    /**
     * @inheritDoc
     */
    public function registerMe() : void 
    {
        parent::registerMe();
        add_action('rest_api_init', array($this, 'registerEndpoints'));
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
     * @param string $path the main namespace for the endpoint (should be unique over the full rest API)
     * the route (after the namespace)
     * @param callable $callback the function which should get after calling the route (should have the return type WP_REST_Response)
     * @param string $method the HTTP Verb (default is GET)
     *
     * @throws IllegalArgumentException if the namepace path is already in use
     *
     * @return void
     */
    final protected function addEndpoint(string $path, callable $callback, string $method=\WP_REST_Server::READABLE): void
    {
        array_push($this->endpoints, array(
            self::KEY_ROUTE => $this->namespace . '/' . $path,
            self::KEY_ARGS => array(
                self::KEY_METHOD => $method,
                self::KEY_CALLBACK => $callback,
                self::KEY_PERMISSION_CALLBACK =>array($this, 'permission'),
            )
        ));
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
}
