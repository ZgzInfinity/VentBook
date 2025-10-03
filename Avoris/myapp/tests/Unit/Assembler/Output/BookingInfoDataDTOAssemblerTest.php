<?php

namespace App\Tests\Unit\Assembler\Output;

use App\Assembler\Output\BookingInfoDataDTOAssembler;
use App\DTO\Output\BookingInfoDataDTO;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Test block responsible for checking correct assembling of BookingInfoDataDTO
 * (Data Transfer object which represents the output data structure of a booking)
 */
class BookingInfoDataDTOAssemblerTest extends TestCase
{
    /**
     * Test responsible for checking if BookingInfoDataDTO is correctly
     * built using the assembler output object class
     * @return void
     */
    public function testTransformReturnsPopulatedDTO(): void
    {
        $params = [
            'reference' => 'ABC123',
            'event_id' => 42,
            'event_date' => '2025-10-01',
            'attendees' => 3,
            'buyer_id' => '12345678A',
        ];

        $assembler = new BookingInfoDataDTOAssembler();
        $dto = $assembler->transform($params);

        $this->assertInstanceOf(BookingInfoDataDTO::class, $dto);
        $this->assertSame('ABC123', $dto->getReference());
        $this->assertSame(42, $dto->getEventId());
        $this->assertEquals(new DateTimeImmutable('2025-10-01'), $dto->getEventDate());
        $this->assertSame(3, $dto->getAttendees());
        $this->assertSame('12345678A', $dto->getIdentification());
    }
}
