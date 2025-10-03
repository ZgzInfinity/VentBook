<?php

namespace App\Tests\Integration\Service;

use App\Service\BookingCancelService;
use App\Repository\BookingRepository;
use App\Repository\EventRepository;
use App\Assembler\Input\BookingCancelDTOAssembler;
use App\DTO\Input\BookingCancelDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Test block for checking booking cancellations process
 */
class BookingCancelServiceTest extends TestCase
{
    /**
     * @var BookingCancelService
     */
    private BookingCancelService $service;

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
    private BookingCancelDTOAssembler $assembler;

    /**
     * Instance a BookingCancelService mock object
     * @return void
     */
    protected function setUp(): void
    {
        $this->bookingRepository = $this->createMock(BookingRepository::class);
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->assembler = $this->createMock(BookingCancelDTOAssembler::class);

        $this->service = new BookingCancelService(
            $this->bookingRepository,
            $this->eventRepository,
            $this->assembler
        );
    }

    /**
     * Test responsible for cheking correct booking creation
     * @return void
     */
    public function testExecuteSuccessfulCancellation(): void
    {
        $params = ['id' => "123"];

        $dto = $this->createMock(BookingCancelDTO::class);
        $dto->method('getId')->willReturn("123");

        $this->assembler->method('transform')->with($params)->willReturn($dto);

        $bookingData = [
            'attendees' => 4,
            'event_id' => 7,
        ];

        $this->bookingRepository->expects($this->once())->method('beginTransaction');
        $this->bookingRepository->expects($this->once())->method('findById')->with(123)->willReturn($bookingData);
        $this->bookingRepository->expects($this->once())->method('deleteById')->with(123)->willReturn(true);
        $this->eventRepository->expects($this->once())->method('updateAvailableSeats')->with(7, 4, true);
        $this->bookingRepository->expects($this->once())->method('commit');
        $this->bookingRepository->expects($this->never())->method('rollBack');

        $this->service->execute($params);
    }

    /**
     * Test for checking booking ID not found
     * @return void
     */
    public function testExecuteThrowsNotFoundExceptionWhenBookingMissing(): void
    {
        $params = ['id' => "123"];
        $dto = $this->createMock(BookingCancelDTO::class);
        $dto->method('getId')->willReturn("123");
        $this->assembler->method('transform')->with($params)->willReturn($dto);

        $this->bookingRepository->expects($this->once())->method('beginTransaction');
        $this->bookingRepository->expects($this->once())->method('findById')->with(123)->willReturn(null);
        $this->bookingRepository->expects($this->once())->method('rollBack');
        $this->bookingRepository->expects($this->never())->method('deleteById');
        $this->bookingRepository->expects($this->never())->method('commit');

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Booking with ID 123 not found');

        $this->service->execute($params);
    }

    /**
     * Test responsible for checking bad booking cancellation
     * @return void
     */
    public function testExecuteThrowsBadRequestExceptionWhenDeleteFails(): void
    {
        $params = ['id' => "123"];
        $dto = $this->createMock(BookingCancelDTO::class);
        $dto->method('getId')->willReturn("123");
        $this->assembler->method('transform')->with($params)->willReturn($dto);

        $bookingData = [
            'attendees' => 4,
            'event_id' => 7,
        ];

        $this->bookingRepository->expects($this->once())->method('beginTransaction');
        $this->bookingRepository->expects($this->once())->method('findById')->with(123)->willReturn($bookingData);
        $this->bookingRepository->expects($this->once())->method('deleteById')->with(123)->willReturn(false);
        $this->bookingRepository->expects($this->once())->method('rollBack');
        $this->bookingRepository->expects($this->never())->method('commit');
        $this->eventRepository->expects($this->never())->method('updateAvailableSeats');

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Booking cannot be cancelled with ID 123');

        $this->service->execute($params);
    }
}
