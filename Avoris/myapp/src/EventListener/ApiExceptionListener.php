<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Listener to catch API exceptions and return a consistent JSON error response.
 *
 * This listener checks if the thrown exception is an instance of {@see ApiException}.
 * If so, it builds a JSON response following a simple error format and assigns it
 * to the Symfony HTTP kernel event.
 */
class ApiExceptionListener
{
    /**
     * Handles kernel exceptions and converts ApiException into JSON responses.
     *
     * @param ExceptionEvent $event The kernel exception event dispatched by Symfony.
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ApiException) {
            $statusCode = $exception->getStatusCode();

            $response = new JsonResponse(
                [
                    'errors' => [
                        [
                            'status' => (string) $statusCode,
                            'title'  => $exception->getMessage(),
                        ]
                    ],
                ],
                $statusCode
            );

            $event->setResponse($response);
        }
    }
}
