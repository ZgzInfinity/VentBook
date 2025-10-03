<?php

namespace App\Tests\Integration\Service;

use App\Service\BookingCreateService;
use App\Repository\BookingRepository;
use App\Repository\EventRepository;
use App\Assembler\Input\BookingCreateDTOAssembler;
use App\DTO\Input\BookingCreateDTO;
use PHPUnit\Framework\TestCase;

/**
 * Test block responsible for creating bookings according to
 * a specific inout data structure
 */
class BookingCreateServiceTest extends TestCase
{
    /**
     * @var BookingCreateService
     */
    private BookingCreateService $service;

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
     * Set an instance of BookingCreateService mock object
     * @return void
     */
    protected function setUp(): void
    {
        $this->bookingRepository = $this->createMock(BookingRepository::class);
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->assembler = $this->createMock(BookingCreateDTOAssembler::class);

        $this->service = new BookingCreateService(
            $this->bookingRepository,
            $this->eventRepository,
            $this->assembler
        );
    }

    /**
     * Test responsible for checking successful booking creation
     * @return void
     */
    public function testExecuteSuccessfulBookingCreation(): void
    {
        $params = ['foo' => 'bar'];

        $dto = $this->createMock(BookingCreateDTO::class);
        $dto->method('getEventId')->willReturn(10);
        $dto->method('getEventDate')->willReturn(new \DateTimeImmutable('2025-12-15'));
        $dto->method('getIdentification')->willReturn('ID123');
        $dto->method('getAttendees')->willReturn(2);
        $dto->method('getReference')->willReturn('ref-abc');

        $this->assembler->method('transform')->with($params)->willReturn($dto);

        $eventData = [
            'from_date' => '2025-12-01',
            'to_date' => '2025-12-31',
            'available_seats' => 5,
        ];
        $this->eventRepository->method('findById')->with(10)->willReturn($eventData);

        $this->bookingRepository->method('findByBuyerIdAndDate')->with(
            $dto->getEventDate(),
            'ID123'
        )->willReturn(null);

        $this->bookingRepository->expects($this->once())->method('beginTransaction');
        $this->bookingRepository->expects($this->once())->method('save')->with($this->callback(function ($data) use ($dto) {
            return
                $data['identification'] === $dto->getIdentification() &&
                $data['reference'] === $dto->getReference() &&
                $data['event_id'] === $dto->getEventId() &&
                $data['event_date'] == $dto->getEventDate() &&
                $data['attendees'] === $dto->getAttendees();
        }));
        $this->eventRepository->expects($this->once())->method('updateAvailableSeats')->with(10, 2);
        $this->bookingRepository->expects($this->once())->method('commit');
        $this->bookingRepository->expects($this->never())->method('rollBack');

        $this->service->execute($params);
    }

    /**
     * Test responsible for checking booking ID not found
     * @return void
     */
    public function testExecuteThrowsExceptionWhenEventNotFound(): void
    {
        $params = [];
        $dto = $this->createMock(BookingCreateDTO::class);
        $dto->method('getEventId')->willReturn(999);
        $this->assembler->method('transform')->with($params)->willReturn($dto);

        $this->eventRepository->method('findById')->with(999)->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Event with ID 999 not found.');

        $this->service->execute($params);
    }

    /**
     * Test responsible for checking if booking date is out of range
     * (lower than initial event date or higher than event date)
     * @return void
     */
    public function testExecuteThrowsExceptionWhenBookingDateOutOfRange(): void
    {
        $params = [];
        $dto = $this->createMock(BookingCreateDTO::class);
        $dto->method('getEventId')->willReturn(10);
        $dto->method('getEventDate')->willReturn(new \DateTimeImmutable('2026-01-01'));
        $this->assembler->method('transform')->with($params)->willReturn($dto);

        $eventData = [
            'from_date' => '2025-12-01',
            'to_date' => '2025-12-31',
            'available_seats' => 5,
        ];
        $this->eventRepository->method('findById')->with(10)->willReturn($eventData);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Booking date must be in range of time event.');

        $this->service->execute($params);
    }

    /**
     * Test responsible for checking possible bookings in a concrete date
     * @return void
     */
    public function testExecuteThrowsExceptionWhenUserHasExistingBooking(): void
    {
        $params = [];
        $dto = $this->createMock(BookingCreateDTO::class);
        $dto->method('getEventId')->willReturn(10);
        $dto->method('getEventDate')->willReturn(new \DateTimeImmutable('2025-12-15'));
        $dto->method('getIdentification')->willReturn('ID123');
        $this->assembler->method('transform')->with($params)->willReturn($dto);

        $eventData = [
            'from_date' => '2025-12-01',
            'to_date' => '2025-12-31',
            'available_seats' => 5,
        ];
        $this->eventRepository->method('findById')->with(10)->willReturn($eventData);

        $this->bookingRepository->method('findByBuyerIdAndDate')->willReturn(['some' => 'booking']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The user with ID ID123 has more than one booking in the date 2025-12-15");

        $this->service->execute($params);
    }

    /**
     * Test responsible for testing if an event has enough free seats
     * according to the seats asked in the booking
     * @return void
     */
    public function testExecuteThrowsExceptionWhenNotEnoughSeats(): void
    {
        $params = [];
        $dto = $this->createMock(BookingCreateDTO::class);
        $dto->method('getEventId')->willReturn(10);
        $dto->method('getEventDate')->willReturn(new \DateTimeImmutable('2025-12-15'));
        $dto->method('getIdentification')->willReturn('ID123');
        $dto->method('getAttendees')->willReturn(10);
        $this->assembler->method('transform')->with($params)->willReturn($dto);

        $eventData = [
            'from_date' => '2025-12-01',
            'to_date' => '2025-12-31',
            'available_seats' => 5,
        ];
        $this->eventRepository->method('findById')->with(10)->willReturn($eventData);

        $this->bookingRepository->method('findByBuyerIdAndDate')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Not enough seats: free 5 seats for 10 asked");

        $this->service->execute($params);
    }

    /**
     * Test responsible for checking rollback error situations in DB
     * @return void
     */
    public function testExecuteRollsBackOnException(): void
    {
        $params = ['foo' => 'bar'];
        $dto = $this->createMock(BookingCreateDTO::class);
        $dto->method('getEventId')->willReturn(10);
        $dto->method('getEventDate')->willReturn(new \DateTimeImmutable('2025-12-15'));
        $dto->method('getIdentification')->willReturn('ID123');
        $dto->method('getAttendees')->willReturn(2);
        $dto->method('getReference')->willReturn('ref-abc');
        $this->assembler->method('transform')->with($params)->willReturn($dto);

        $eventData = [
            'from_date' => '2025-12-01',
            'to_date' => '2025-12-31',
            'available_seats' => 5,
        ];
        $this->eventRepository->method('findById')->with(10)->willReturn($eventData);

        $this->bookingRepository->method('findByBuyerIdAndDate')->willReturn(null);

        $this->bookingRepository->expects($this->once())->method('beginTransaction');
        $this->bookingRepository->expects($this->once())->method('save')->willThrowException(new \Exception("DB error"));
        $this->bookingRepository->expects($this->once())->method('rollBack');
        $this->bookingRepository->expects($this->never())->method('commit');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("DB error");

        $this->service->execute($params);
    }
}
