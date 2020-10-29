<?php

namespace Smll\Url;

use Smll\Http\Request;
use Smll\Http\Server;

class Url
{
    public function __construct()
    {
    }

    /**
     * get current url
     * @param string $path
     * @return string $path
     */
    public static function path(string $path)
    {
        return Request::baseUrl() . trim($path, '/');
    }

    /**
     * Get the previous url
     * @return mixed|null
     */
    public static function previous()
    {
        return Server::get('HTTP_REFERER');
    }

    /**
     * Redirect to given URL
     * @param $url
     */
    public static function redirect($url)
    {
        header('location: ' . $url);
        exit();
    }
}
