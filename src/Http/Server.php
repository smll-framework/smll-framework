<?php


namespace Smll\Http;


class Server
{
    /**
     * Server constructor.
     */
    public function __construct()
    {
    }

    /**
     * Get all server data
     *
     * @return array
     */
    public static function all()
    {
        return $_SERVER;
    }

    /**
     * Check that server has the given key
     *
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        return isset($_SERVER[$key]);
    }

    /**
     * Get the value from server by the given key
     *
     * @param string $key
     * @return mixed|null
     */
    public static function get(string $key)
    {
        return static::has($key) ? $_SERVER[$key] : null;
    }

    /**
     * @param $path
     * @return array
     */
    public static function path_info($path)
    {
        return pathinfo($path);
    }
}
