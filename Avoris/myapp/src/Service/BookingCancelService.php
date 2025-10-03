<?php

namespace App\Service;

use App\Assembler\Input\BookingCancelDTOAssembler;
use App\Repository\BookingRepository;
use App\Repository\EventRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Service responsible for cancel a booking of user
 */
class BookingCancelService
{
    /**
     * @var BookingRepository
     */
    private BookingRepository $bookingRepository;

    /**
     * @var EventRepository
     */
    private EventRepository $eventRepository;

    /**
     * @var BookingCancelDTOAssembler
     */
    private BookingCancelDTOAssembler $bookingCancelDTOAssembler;

    /**
     * @param BookingRepository $bookingRepository
     * @param EventRepository $eventRepository
     * @param BookingCancelDTOAssembler $bookingCancelDTOAssembler
     */
    public function __construct(
        BookingRepository $bookingRepository,
        EventRepository $eventRepository,
        BookingCancelDTOAssembler $bookingCancelDTOAssembler
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->eventRepository = $eventRepository;
        $this->bookingCancelDTOAssembler = $bookingCancelDTOAssembler;
    }

    /**
     * Exceutes the cancelation of a booking for user
     * @param array $params
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function execute(array $params): void
    {
        $dto = $this->bookingCancelDTOAssembler->transform($params);
        $bookingId = $dto->getId();

        $this->bookingRepository->beginTransaction();

        try {
            $booking = $this->bookingRepository->findById($bookingId);

            if (!$booking) {
                throw new NotFoundHttpException("Booking with ID {$bookingId} not found");
            }

            $attendees = (int) $booking['attendees'];
            $eventId = (int) $booking['event_id'];

            $success = $this->bookingRepository->deleteById($bookingId);

            if (!$success) {
                throw new BadRequestHttpException("Booking cannot be cancelled with ID {$bookingId}");
            }

            $this->eventRepository->updateAvailableSeats($eventId, $attendees, true);

            $this->bookingRepository->commit();
        } catch (\Exception $e) {
            $this->bookingRepository->rollBack();
            throw $e;
        }
    }
}
