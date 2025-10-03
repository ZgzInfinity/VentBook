<?php

namespace App\Controller;

use App\Service\EventDisplayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'event_')]
class EventDisplayController extends AbstractController
{
    #[Route('/events/{id}', name: 'display', methods: ['GET'])]
    public function __invoke(int $id, EventDisplayService $eventDisplayService): JsonResponse
    {
        try {
            $eventData = $eventDisplayService->execute(['id' => $id]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => "Internal server error",
                'errors' => $e->getMessage()
            ], 500);
        }

        if ($eventData === null) {
            return $this->json([
                'success' => false,
                'message' => "Event not found"
            ], 404);
        }

        return $this->json([
            'success' => "Event displayed successfuly",
            'data' => $eventData,
        ], 200);
    }
}
