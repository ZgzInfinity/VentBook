<?php

namespace App\Controller;

use App\Service\EventOptionalFilterDisplayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'event_filter_')]
class EventFilterController extends AbstractController
{
    #[Route('/events', name: 'list', methods: ['GET'])]
    public function __invoke(Request $request, EventOptionalFilterDisplayService $eventService): JsonResponse
    {
        try {
            $filters = $request->query->all();

            if (isset($filters['filters'])) {
                $filters['filters'] = json_decode($filters['filters'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => "Invalid filters format"
                    ], 400);
                }
            }

            try {
                $events = $eventService->execute($filters);
            } catch (\InvalidArgumentException $e) {
                return new JsonResponse([
                    'success' => false,
                    'message' => "Unknown filters params"
                ], 422);
            }

            if ($events === null) {
                return new JsonResponse([
                    'success' => true,
                    'message' => "No events found"
                ], 404);
            }

            return $this->json(
                [
                    'success' => true,
                    'message' => $events,
                ],
                200
            );
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => "Internal server error",
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
