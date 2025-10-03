<?php

namespace App\Tests\Unit\Assembler\Output;

use App\Assembler\Output\EventInfoDataDTOAssembler;
use App\DTO\Output\EventInfoDataDTO;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Test block responsible for checking correct assembling of EventInfoDataDTO
 * (Data Transfer object which represents the output data structure of an event)
 */
class EventInfoDataDTOAssemblerTest extends TestCase
{
    /**
     * @var array
     */
    private array $baseParams;

    /**
     * Set a default output structure array of an event
     * @return void
     */
    protected function setUp(): void
    {
        $this->baseParams = [
            'name' => 'Music Festival',
            'description' => 'A great music event',
            'from_date' => '2025-09-10',
            'to_date' => '2025-09-12',
            'available_seats' => 100,
        ];
    }

    /**
     * Test responsible for checking if v is correctly
     * built using the assembler output object class
     * @return void
     */
    public function testTransformWithDescription(): void
    {
        $assembler = new EventInfoDataDTOAssembler();
        $dto = $assembler->transform($this->baseParams);

        $this->assertInstanceOf(EventInfoDataDTO::class, $dto);
        $this->assertSame('Music Festival', $dto->getName());
        $this->assertSame('A great music event', $dto->getDescription());
        $this->assertEquals(new DateTimeImmutable('2025-09-10'), $dto->getFromDate());
        $this->assertEquals(new DateTimeImmutable('2025-09-12'), $dto->getToDate());
        $this->assertSame(100, $dto->getAvailableSeats());
    }

    public function testTransformWithoutDescription(): void
    {
        $assembler = new EventInfoDataDTOAssembler();
        $dto = $assembler->transform($this->baseParams, false);

        $this->assertInstanceOf(EventInfoDataDTO::class, $dto);
        $this->assertSame('Music Festival', $dto->getName());
        $this->assertNull($dto->getDescription());
        $this->assertEquals(new DateTimeImmutable('2025-09-10'), $dto->getFromDate());
        $this->assertEquals(new DateTimeImmutable('2025-09-12'), $dto->getToDate());
        $this->assertSame(100, $dto->getAvailableSeats());
    }
}
