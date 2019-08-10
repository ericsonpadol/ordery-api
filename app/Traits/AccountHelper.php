<?php

namespace App\Traits;

use DB;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

trait AccountHelper
{
    public static function uuidGeneration()
    {
        try {
            $uuid = Uuid::uuid4();
            return $uuid->toString();
        } catch(Exception $e) {
            return [
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'http_code' => StatusHttp::getStatusCode500()
            ];
        }
    }

    public static function uuidStoreKeyGeneration()
    {
        try {
            $uuid = Uuid::uuid1();
            return $uuid->toString();
        } catch(Exception $e) {
            return [
                'message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'http_code' => StatusHttp::getStatusCode500()
            ];
        }
    }
}