<?php

namespace App\Exception;

/**
 * Exception thrown when a requested resource is not found.
 *
 * Extends the base ApiException with a default 404 HTTP status code.
 */
class NotFoundException extends ApiException
{
    /**
     * NotFoundException constructor.
     *
     * @param string
     */
    public function __construct(string $message = 'Not Found')
    {
        parent::__construct(404, $message);
    }
}
