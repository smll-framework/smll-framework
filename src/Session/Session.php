<?php

namespace Smll\Session;

class Session
{
    /**
     * Session constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * Session start
     *
     * @return void
     */

    public static function start()
    {
        if (!session_id()) {
            ini_set('session.use_only_cookies', 1);
            session_start();
        }
    }

    /**
     * Set new session
     *
     * @param string $key
     * @param $value
     *
     * @return string $value
     */
    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;

        return $value;
    }

    /**
     * Check that session has the given key
     *
     * @param string $key
     *
     * @return bool
     */
    public static function has(string $key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Get session value of the given key
     *
     * @param string $key
     *
     * @return mixed
     */
    public static function get(string $key)
    {
        return $_SESSION[$key];
    }

    /**
     * Removes that session by the given key
     *
     * @param string $key
     */
    public static function remove(string $key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Get all session attributes
     *
     * @return array
     */
    public static function all()
    {
        return $_SESSION;
    }

    /**
     * Destroy the session
     *
     * @return void
     */
    public static function destroy()
    {
        foreach (static::all() as $key => $value) {
            static::remove($key);
        }
    }

    /**
     * Flash the session by the given key
     * @param $key
     * @return mixed|null
     */
    public static function flash($key)
    {
        $value = null;
        if (static::has($key)) {
            $value = static::get($key);
            static::remove($key);
        }
        return $value;
    }
}
