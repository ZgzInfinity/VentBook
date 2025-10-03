<?php

namespace App\Controller;

use App\Service\BookingCancelService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[Route(name: 'booking_')]
class BookingCancelController extends AbstractController
{
    #[Route('/bookings/{id}/cancel', name: 'cancel', methods: ['DELETE'])]
    public function __invoke(
        string $id,
        BookingCancelService $bookingCancelService
    ): JsonResponse {
        $params = ['id' => $id];

        try {
            $bookingCancelService->execute($params);
        }
        catch (BadRequestHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => "Invalid booking id",
                'errors' => $e->getMessage(),
            ], 400);
        }
        catch (NotFoundHttpException $e) {
            return $this->json([
                'success' => false,
                'message' => "Booking not found",
                'errors' => $e->getMessage(),
            ], 404);
        }
        catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => "Internal server error",
                'errors' => $e->getMessage(),
            ], 500);
        }

        return $this->json([
            'success' => true,
            'message' => "Booking cancelled successfully"
        ], 200);
    }
}
