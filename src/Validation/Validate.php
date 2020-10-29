<?php

namespace Smll\Validation;
use Rakit\Validation\Validator;
use Smll\Http\Request;
use Smll\Session\Session;
use Smll\Url\Url;
use Smll\Validation\Rules\Unique;


class Validate
{
    public function __construct()
    {
    }

    public static function validate($data, $rules, $redirect)
    {
        $validator = new Validator;
        $validator->addValidator('unique', new Unique());
        $validation = $validator->validate($data, $rules);

        $errors = $validation->errors();

        if ($validation->fails()) {
            if($redirect) {
                return ['errors' => $errors->firstOfAll()];
            } else {
                Session::set('errors', $errors->firstOfAll());
                Session::set('old', (string)Request::all());
                return Url::redirect(Url::previous());
            }
        }
    }
}
