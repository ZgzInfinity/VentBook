<?php

namespace App\Exception;

/**
 * Exception thrown when access to a resource is forbidden.
 *
 * Extends the base ApiException with a default 403 HTTP status code.
 */
class ForbiddenException extends ApiException
{
    /**
     * ForbiddenException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = 'Forbidden')
    {
        parent::__construct(403, $message);
    }
}
