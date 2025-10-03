<?php

namespace App\Tests\Integration\Service;

use App\Assembler\Input\BookingListingDTOAssembler;
use App\Assembler\Output\BookingInfoDataDTOAssembler;
use App\DTO\Input\BookingListingDTO;
use App\DTO\Output\BookingInfoDataDTO;
use App\Repository\BookingRepository;
use App\Service\BookingListingService;
use PHPUnit\Framework\TestCase;

/**
 * Test block responsible for checking listing bookings by user ID
 */
class BookingListingServiceTest extends TestCase
{
    /**
     * @var BookingRepository
     */
    private BookingRepository $bookingRepository;

    /**
     * @var BookingListingDTOAssembler
     */
    private BookingListingDTOAssembler $inputDTOAssembler;

    /**
     * @var BookingInfoDataDTOAssembler
     */
    private BookingInfoDataDTOAssembler $outputDTOAssembler;

    /**
     * @var BookingListingService
     */
    private BookingListingService $service;

    /**
     * Set an instance of BookingListingService mock object
     * @return void
     */
    protected function setUp(): void
    {
        $this->bookingRepository = $this->createMock(BookingRepository::class);
        $this->inputDTOAssembler = $this->createMock(BookingListingDTOAssembler::class);
        $this->outputDTOAssembler = $this->createMock(BookingInfoDataDTOAssembler::class);

        $this->service = new BookingListingService(
            $this->bookingRepository,
            $this->inputDTOAssembler,
            $this->outputDTOAssembler
        );
    }

    /**
     * Test responsible for checking bookings array format response
     * @return void
     */
    public function testExecuteReturnsArrayOfDTOs(): void
    {
        $params = ['identification' => 'USER123'];

        $dto = $this->createMock(BookingListingDTO::class);
        $dto->method('getIdentification')->willReturn('USER123');

        $this->inputDTOAssembler
            ->method('transform')
            ->with($params)
            ->willReturn($dto);

        $bookingData = [
            ['reference' => 'REF1', 'event_id' => 1, 'event_date' => '2025-08-10', 'attendees' => 2, 'buyer_id' => 'USER123'],
            ['reference' => 'REF2', 'event_id' => 2, 'event_date' => '2025-08-12', 'attendees' => 1, 'buyer_id' => 'USER123'],
        ];

        $this->bookingRepository
            ->method('findByIdentification')
            ->with('USER123')
            ->willReturn($bookingData);

        $dto1 = $this->createMock(BookingInfoDataDTO::class);
        $dto2 = $this->createMock(BookingInfoDataDTO::class);

        $this->outputDTOAssembler
            ->method('transform')
            ->willReturnOnConsecutiveCalls($dto1, $dto2);

        $result = $this->service->execute($params);

        $this->assertCount(2, $result);
        $this->assertSame([$dto1, $dto2], $result);
    }

    /**
     * Test responsible for checking unavailable bookngs array
     * (user with a certain identification has not made any booking)
     * @return void
     */
    public function testExecuteReturnsNullWhenNoBookings(): void
    {
        $params = ['identification' => 'USER123'];

        $dto = $this->createMock(BookingListingDTO::class);
        $dto->method('getIdentification')->willReturn('USER123');

        $this->inputDTOAssembler
            ->method('transform')
            ->with($params)
            ->willReturn($dto);

        $this->bookingRepository
            ->method('findByIdentification')
            ->with('USER123')
            ->willReturn([]);

        $result = $this->service->execute($params);

        $this->assertNull($result);
    }
}
