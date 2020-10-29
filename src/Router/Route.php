<?php

namespace Smll\Router;

use Exception;
use Smll\Http\Request;

class Route
{
    /**
     * Route Container
     * @var array $routes
     */
    private static $routes = [];

    /**
     * Middleware
     * @var string $middleware ;
     */
    private static $middleware = '';

    /**
     * Prefix
     * @var string $prefix ;
     */
    private static $prefix = '';

    /**
     * Route constructor.
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Add route
     * @param string $methods
     * @param string $uri
     * @param object|callback $callback
     */
    public static function add(string $methods, string $uri, $callback)
    {
        $uri = rtrim(static::$prefix . '/' . trim($uri, '/'), '/');
        $uri = $uri ?: '/';
        foreach (explode('|', $methods) as $method) {
            static::$routes[] = [
                'uri' => $uri,
                'callback' => $callback,
                'method' => $method,
                'middleware' => static::$middleware,
            ];
        }
    }

    /**
     * Add new get route
     * @param string $uri
     * @param object|callback $callback
     */
    public static function get(string $uri, $callback)
    {
        static::add('GET', $uri, $callback);
    }

    /**
     * Add new post route
     * @param string $uri
     * @param object|callback $callback
     */
    public static function post(string $uri, $callback)
    {
        static::add('POST', $uri, $callback);
    }

    /**
     * Add new any route
     * @param string $uri
     * @param object|callback $callback
     */
    public static function any(string $uri, $callback)
    {
        static::add('GET|POST', $uri, $callback);
    }

    /**
     * Get All Routes
     * @return array
     */
    public static function all()
    {
        return static::$routes;
    }

    /**
     * Set prefix for routing
     * @param string $prefix
     * @param $callback
     * @throws Exception
     */
    public static function prefix(string $prefix, $callback)
    {
        $parent_prefix = static::$prefix;
        static::$prefix .= '/' . trim($prefix, '/');
        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \BadFunctionCallException('Provide a valid callback function');
        }

        static::$prefix = $parent_prefix;
    }

    /**
     * Set middleware for routing
     * @param string $middleware
     * @param $callback
     * @throws Exception
     */
    public static function middleware(string $middleware, $callback)
    {
        $parent_middleware = static::$middleware;
        static::$middleware .= '|' . trim($middleware, '|');
        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \BadFunctionCallException('Provide a valid callback function');
        }

        static::$middleware = $parent_middleware;
    }

    /**
     * Handle the request and match the route
     * @return array
     */
    public static function handle()
    {
        $uri = Request::url();
        foreach (static::$routes as $route) {
            $matched = true;
            $route['uri'] = preg_replace('/\/:(.*?):/', '/(.*?)', $route['uri']);
            $route['uri'] = '#^' . $route['uri'] . '$#';

            if (preg_match($route['uri'], $uri, $matches)) {
                array_shift($matches);
                $params = array_values($matches);

                foreach ($params as $param) {
                    if (strpos($param, '/')) {
                        $matched = false;
                    }
                }

                if ($route['method'] != Request::method()) {
                    $matched = false;
                }

                if ($matched) {
                    return static::invoke($route, $params);
                }
            }
        }

        die('Not found page');
    }

    /**
     * Invoke the route
     * @param array $route
     * @param array $params
     * @return mixed
     * @throws \ReflectionException
     */

    public static function invoke(array $route, array $params = [])
    {
        static::executeMiddleware($route);
        $callback = $route['callback'];

        if (is_callable($callback)) {

            return call_user_func_array($callback, $params);

        } elseif (strpos($callback, '@') !== false) {

            list($controller, $method) = explode('@', $callback);

            $controller = 'App\Controllers\\' . $controller;

            if (class_exists($controller)) {
                $object = new $controller;

                if (method_exists($object, $method)) {
                    return call_user_func_array([$object, $method], $params);
                }
                throw new \BadFunctionCallException("The method $method() is not exists at $controller");
            }
            throw new \BadMethodCallException("Class $controller couldn't found.");

        }
        throw new \ReflectionException("Please Provide a Valid Callback Function");
    }

    public static function executeMiddleware($route)
    {
        foreach (explode('|', $route['middleware']) as $middleware) {

            if ($middleware != '') {
                $middleware = 'App\Middleware\\' . $middleware;

                if (class_exists($middleware)) {
                    $object = new $middleware;

                    if (method_exists($object, 'handle')) {
                        return call_user_func_array([$object, 'handle'], []);
                    }
                    throw new \ReflectionException("The handle() method couldn't found at $middleware");
                }
                throw new \ReflectionException("Class $middleware couldn't not found");
            }
        }
    }
}
