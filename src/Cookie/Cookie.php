<?php

namespace Smll\Cookie;

class Cookie
{
    /**
     * Cookie constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * Set new cookie
     *
     * @param string $key
     * @param string $value
     *
     * @return string $value
     */
    public static function set(string $key, string $value)
    {
        $expired = time() + (1 * 365 * 24 * 60 * 60);
        setcookie($key, $value, $expired, '/', '', false, true );

        return $value;
    }

    /**
     * Check that cookie has the given key
     *
     * @param string $key
     *
     * @return bool
     */
    public static function has(string $key)
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * Get cookie value of the given key
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {
        return static::has($key) ? $_COOKIE[$key] : null;
    }

    /**
     * Removes that cookie by the given key
     *
     * @param string $key
     */
    public static function remove(string $key)
    {
        unset($_COOKIE[$key]);
    }

    /**
     * Get all cookie attributes
     *
     * @return array
     */
    public static function all()
    {
        return $_COOKIE;
    }

    /**
     * Destroy the cookies
     *
     * @return void
     */
    public static function destroy()
    {
        foreach (static::all() as $key => $value) {
            static::remove($key);
        }
    }
}
