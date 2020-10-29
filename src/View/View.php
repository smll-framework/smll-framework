<?php

namespace Smll\View;

use Smll\File\File;
use Latte\Engine;
use Smll\Session\Session;

class View
{
    /**
     * View constructor.
     */
    public function __construct()
    {
    }

    /**
     * Render the latte view
     * @param string $path
     * @param array $data
     * @throws \Exception
     */
    public static function render(string $path, array $data = [])
    {
        $errors = Session::flash('errors');
        $old = Session::flash('old');
        array_merge($data, ['errors' => $errors, 'old' => $old]);
        $latte = new Engine();
        $latte->setTempDirectory(File::path('/storage/caches'));
        $path = 'views' . File::ds() . str_replace(['/', '\\', '.'], File::ds(), $path) . '.latte';
        $latte->render(File::path($path), $data);
    }
}
