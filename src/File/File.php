<?php

namespace Smll\File;

class File
{
    /**
     * File constructor.
     */
    public function __construct()
    {
    }

    /**
     * Root path
     * @return string
     */
    public static function root()
    {
        return ROOT;
    }

    /**
     * Directory separator
     * @return string
     */
    public static function ds()
    {
        return DS;
    }

    /**
     * Get file full path
     * @param string $path
     * @return string
     */
    public static function path(string $path)
    {
        $path = static::root() . static::ds() . trim($path, '/');
        $path = str_replace(['/', '\\'], static::ds(), $path);
        return $path;
    }

    /**
     * Check the given file is exists
     * @param string $path
     * @return bool
     */
    public static function exists(string $path)
    {
        return file_exists(static::path($path));
    }

    /**
     * Require File
     * @param string $path
     * @return mixed
     */
    public static function require_file(string $path)
    {
        if (static::exists($path)) {
            return require_once static::path($path);
        }

        return false;
    }

    /**
     * Include File
     * @param string $path
     * @return mixed
     */
    public static function include_file(string $path)
    {
        if (static::exists($path)) {
            return include static::path($path);
        }

        return false;
    }

    /**
     * Require Directory
     * @param string $path
     * @return mixed
     */
    public static function require_dir(string $path)
    {
        $files = array_diff(scandir(static::path($path)), ['..', '.']);

        foreach ($files as $file) {
            $file_path = $path . static::ds() . $file;

            if (static::exists($file_path)) {
               static::require_file($file_path);
            }
        }
    }
}
