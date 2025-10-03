<?php

namespace App\Tests\Unit\DTO\Output;

use App\DTO\Output\BookingInfoDataDTO;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Test block for checking Output BookingInfoDataDTO
 * (Data object which represents the output structure of a booking)
 */
class BookingInfoDataDTOTest extends TestCase
{
    /**
     * Test responsible for checking creations of BookingInfoDataDTO objects
     * with all fields filled properly
     * @return void
     */
    public function testCanSetAndGetValues(): void
    {
        $dto = new BookingInfoDataDTO();

        $identification = '12345678A';
        $reference = 'ABC123XYZ';
        $eventId = 42;
        $eventDate = new DateTimeImmutable('2025-10-01 15:00');
        $attendees = 3;

        $dto->setIdentification($identification);
        $dto->setReference($reference);
        $dto->setEventId($eventId);
        $dto->setEventDate($eventDate);
        $dto->setAttendees($attendees);

        $this->assertEquals($identification, $dto->getIdentification());
        $this->assertEquals($reference, $dto->getReference());
        $this->assertEquals($eventId, $dto->getEventId());
        $this->assertSame($eventDate, $dto->getEventDate());
        $this->assertEquals($attendees, $dto->getAttendees());
    }

    /**
     * Test responsible for checking if dates of event are correctly
     * @return void
     */
    public function testEventDateReturnsDateTimeImmutable(): void
    {
        $dto = new BookingInfoDataDTO();
        $date = new DateTimeImmutable('2025-11-11');
        $dto->setEventDate($date);

        $this->assertInstanceOf(DateTimeImmutable::class, $dto->getEventDate());
        $this->assertSame('2025-11-11', $dto->getEventDate()->format('Y-m-d'));
    }
}
