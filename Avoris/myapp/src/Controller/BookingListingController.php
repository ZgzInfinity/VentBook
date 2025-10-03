<?php

namespace App\Controller;

use App\Service\BookingListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(name: 'booking_')]
class BookingListingController extends AbstractController
{
    #[Route('/bookings/{id}', name: 'list', methods: ['GET'])]
    public function __invoke(string $id, BookingListingService $bookingListingService, SerializerInterface $serializer): JsonResponse
    {
        try {
            if (!$id) {
                return $this->json([
                    'success' => false,
                    'message' => "Invalid input identification format"
                ], 400);
            }

            $result = $bookingListingService->execute(['id' => $id]);

            if ($result === null) {
                return $this->json([
                    'success' => false,
                    'message' => "Invalid input identification data"
                ], 422);
            }

            $normalizedEvents = $serializer->normalize($result, null, [
                'datetime_format' => 'Y-m-d',
            ]);

            return $this->json(
                [
                    'success' => true,
                    'message' => $normalizedEvents
                ],
                200
            );
        }
        catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => "Internal server error",
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
