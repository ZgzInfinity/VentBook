<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Base API exception class that extends Symfony's HttpException.
 *
 * Used to represent HTTP errors with a status code and message.
 */
class ApiException extends HttpException
{
    /**
     * ApiException constructor.
     *
     * @param int $statusCode
     * @param string $message
     */
    public function __construct(int $statusCode, string $message = '')
    {
        parent::__construct($statusCode, $message);
    }
}

