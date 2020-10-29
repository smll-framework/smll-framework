<?php


namespace Smll\Http;


class Response
{
    /**
     * Response constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * Output the data
     * @param $data
     */
    public static function output($data)
    {
        if (! $data) { return; }

        if(!is_string($data)) {
            $data = json_encode($data);
        }

        echo $data;
    }

    /**
     * Return Json Data
     * @param $data
     * @return mixed
     */
    public static function json($data) {
        return json_encode($data);
    }
}
