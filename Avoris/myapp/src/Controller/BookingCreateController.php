<?php

namespace App\Controller;

use App\Service\BookingCreateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'booking_')]
class BookingCreateController extends AbstractController
{
    #[Route('/bookings', name: 'create', methods: ['POST'])]
    public function __invoke(Request $request, BookingCreateService $bookingCreateService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse([
                'success' => false,
                'message' => "Invalid booking format"
            ], 400);
        }

        try {
            $bookingCreateService->execute($data);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'success' => false,
                'message' => "Invalid input data",
                'errors' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => "Internal server error",
                'errors' => $e->getMessage(),
            ], 500);
        }

        return $this->json(
            [
                'success' => true,
                'message' => "Booking successfuly created"
            ],
            201
        );
    }
}
