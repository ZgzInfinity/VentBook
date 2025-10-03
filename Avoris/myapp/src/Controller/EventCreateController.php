<?php

namespace App\Controller;

use App\Service\EventCreateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[Route(name: 'event_')]
class EventCreateController extends AbstractController
{
    #[Route('/events', name: 'create', methods: ['POST'])]
    public function __invoke(Request $request, EventCreateService $eventCreateService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json([
                'success' => false,
                'message' => "Invalid event format"
            ], 400);
        }

        try {
            $eventId = $eventCreateService->execute($data);
        } catch (ValidationFailedException $e) {
            return $this->json([
                'success' => false,
                'message' => "Unknown event format",
                'errors' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => "Internal server error",
                'errors' => $e->getMessage()
            ], 500);
        }

        return $this->json([
            'success' => true,
            'event_id' => $eventId
        ], 201);
    }
}
