<?php
namespace Smll\Exceptions;

class Whoops
{
    /**
     * Whoops constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * Handles the whoops error
     *
     * @return void
     */
    public static function handle() {
        $whoops = new \Whoops\Run;
        $whoops->prependHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }
}
