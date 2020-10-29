<?php

namespace Smll\Bootstrap;

use Smll\Exceptions\Whoops;
use Smll\File\File;
use Smll\Http\Request;
use Smll\Http\Response;
use Smll\Router\Route;
use Smll\Session\Session;

class App
{

    /**
     * App constructor.
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Run the application
     *
     * @return array
     */
    public static function run()
    {
        // Register Whoops
        Whoops::handle();

        //Start Session
        Session::start();

        //Handle Request
        Request::handle();

        //Require all routes
        File::require_dir('routes');

        $data =  Route::handle();

        Response::output($data);
    }
}
