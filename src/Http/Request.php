<?php


namespace Smll\Http;


class Request
{
    /**
     * Base url
     * @var $base_url
     */
    private static $base_url;

    /**
     * Url
     * @var $url
     */
    private static $url;

    /**
     * Full url
     * @var $full_url
     */
    private static $full_url;

    /**
     * Query String
     * @var $query_string
     */
    private static $query_string;

    /**
     * @var $script_name
     */
    private static $script_name;

    /**
     * Request constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * Handle the request
     * @return void
     */
    public static function handle()
    {
        static::$script_name = str_replace('\\', '', dirname(Server::get('SCRIPT_NAME')));
        static::setBaseUrl();
        static::setUrl();
    }

    /**
     * Set Base Url
     * @return void
     */
    private static function setBaseUrl()
    {
        $protocol = Server::get('REQUEST_SCHEME') . '://';
        $host = Server::get('HTTP_HOST');
        $script_name = static::$script_name;

        static::$base_url = $protocol . $host . $script_name;
    }

    /**
     * Set Url
     * @return void
     */
    private static function setUrl()
    {
        $request_uri = urldecode(Server::get('REQUEST_URI'));
        $query_string = '';
        static::$full_url = $request_uri;

        if (strpos($request_uri, '?') !== false) {
            list($request_uri, $query_string) = explode('?', $request_uri);
        }

        static::$url = $request_uri ?: '/';
        static::$query_string = $query_string;
    }

    /**
     * Get Base Url
     * @return string
     */
    public static function baseUrl()
    {
        return static::$base_url;
    }

    /**
     * Get Url
     * @return string
     */
    public static function url()
    {
        return static::$url;
    }

    /**
     * Get Query String
     * @return string
     */
    public static function query_string()
    {
        return static::$query_string;
    }

    /**
     * Get Full Url
     * @return string
     */
    public static function full_url()
    {
        return static::$full_url;
    }

    /**
     * Get Request Method
     * @return string
     */
    public static function method()
    {
        return Server::get('REQUEST_METHOD');
    }

    /**
     * Check that the given key exist on the request
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        return array_key_exists($key, $_REQUEST);
    }

    /**
     * Get value from GET Request
     * @param $key
     * @return string
     */
    public static function get($key)
    {
        return static::has($key) ? $_GET[$key] : null;
    }

    /**
     * Get value from POST Request
     * @param $key
     * @return string
     */
    public static function post($key)
    {
        return static::has($key) ? $_POST[$key] : null;
    }

    /**
     * Set value for request by the given key
     * @param $key
     * @param $value
     * @return string
     */
    public static function set($key, $value)
    {
        $_GET[$key] = $value;
        $_POST[$key] = $value;
        $_REQUEST[$key] = $value;

        return $value;
    }

    /**
     * Get the previous request value
     * @return string
     */
    public static function previous()
    {
        return Server::get('HTTP_REFERER');
    }

    /**
     * Get request all
     * @return array
     */
    public static function all()
    {
        return $_REQUEST;
    }
}
