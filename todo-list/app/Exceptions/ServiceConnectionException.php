<?php

namespace App\Exceptions;

use Exception;

class ServiceConnectionException extends Exception
{
    protected $code = 503;
}