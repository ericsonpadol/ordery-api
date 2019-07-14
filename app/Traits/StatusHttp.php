<?php
namespace App\Traits;

trait StatusHttp
{
    public static function getStatusCode200()
    {
        return 200;
    }

    public static function getStatusCode500()
    {
        return 500;
    }

    public static function getStatusCode401()
    {
        return 401;
    }

    public static function getStatusCode404()
    {
        return 404;
    }

    public static function getStatusCode422()
    {
        return 422;
    }

    public static function getStatusCode400()
    {
        return 400;
    }
}