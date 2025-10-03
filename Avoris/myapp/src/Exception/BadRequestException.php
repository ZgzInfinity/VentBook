<?php

namespace App\Exception;

/**
 * Exception thrown when a bad request is made to the API.
 *
 * Extends the base ApiException with a default 400 HTTP status code.
 */
class BadRequestException extends ApiException
{
    /**
     * BadRequestException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = 'Bad Request')
    {
        parent::__construct(400, $message);
    }
}
