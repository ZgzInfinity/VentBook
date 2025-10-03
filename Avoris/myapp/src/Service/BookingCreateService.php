<?php

namespace App\Service;

use App\Assembler\Input\BookingCreateDTOAssembler;
use App\Repository\BookingRepository;
use App\Repository\EventRepository;

/**
 * Service responsible for handling the business logic of creating new bookings.
 */
class BookingCreateService
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
     * @var BookingCreateDTOAssembler
     */
    private BookingCreateDTOAssembler $assembler;

    /**
     * @param BookingRepository         $bookingRepository
     * @param EventRepository           $eventRepository
     * @param BookingCreateDTOAssembler $assembler
     */
    public function __construct(
        BookingRepository $bookingRepository,
        EventRepository $eventRepository,
        BookingCreateDTOAssembler $assembler
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->eventRepository = $eventRepository;
        $this->assembler = $assembler;
    }

    /**
     * Executes the booking creation flow.
     * @param array $params
     * @return void
     * @throws \InvalidArgumentException 
     * @throws \Exception                
     */
    public function execute(array $params): void
    {
        $bookingDTO = $this->assembler->transform($params);

        $event = $this->eventRepository->findById($bookingDTO->getEventId());
        if (!$event) {
            throw new \InvalidArgumentException("Event with ID {$bookingDTO->getEventId()} not found.");
        }

        $eventFrom    = $event['from_date'];
        $eventTo      = $event['to_date'];
        $bookingDate  = $bookingDTO->getEventDate()->format('Y-m-d');
        if ($bookingDate < $eventFrom || $bookingDate > $eventTo) {
            throw new \InvalidArgumentException("Booking date must be in range of time event.");
        }

        $existingBooking = $this->bookingRepository->findByBuyerIdAndDate(
            $bookingDTO->getEventDate(),
            $bookingDTO->getIdentification()
        );
        if ($existingBooking !== null) {
            throw new \InvalidArgumentException("The user with ID {$bookingDTO->getIdentification()} has more than one booking in the date {$bookingDate}.");
        }

        $availableSeats = (int) $event['available_seats'];
        $requestedSeats = $bookingDTO->getAttendees();
        if ($availableSeats === 0 || $requestedSeats > $availableSeats) {
            throw new \InvalidArgumentException("Not enough seats: free {$availableSeats} seats for {$requestedSeats} asked");
        }

        $this->bookingRepository->beginTransaction();

        try {
            $this->bookingRepository->save([
                'identification' => $bookingDTO->getIdentification(),
                'reference'      => $bookingDTO->getReference(),
                'event_id'       => $bookingDTO->getEventId(),
                'event_date'     => $bookingDTO->getEventDate(),
                'attendees'      => $bookingDTO->getAttendees(),
            ]);

            $this->eventRepository->updateAvailableSeats(
                $bookingDTO->getEventId(),
                $requestedSeats
            );

            $this->bookingRepository->commit();
        } catch (\Exception $e) {
            $this->bookingRepository->rollBack();
            throw $e;
        }
    }
}
