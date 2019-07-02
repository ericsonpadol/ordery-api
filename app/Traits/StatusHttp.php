<?php
namespace App\Traits;

trait StatusHttp
{
    public function getStatusCode200()
    {
        return 200;
    }

    public function getStatusCode500()
    {
        return 500;
    }

    public function getStatusCode401()
    {
        return 401;
    }

    public function getStatusCode404()
    {
        return 404;
    }

    public function getStatusCode422()
    {
        return 422;
    }

    public function getStatusCode400()
    {
        return 400;
    }
}