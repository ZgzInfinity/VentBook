<?php

namespace App\Tests\Unit\DTO\Output;

use App\DTO\Output\EventInfoDataDTO;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Test block for checking Output EventInfoDataDTO
 * (Data object which represents the output structure of an event)
 */
class EventInfoDataDTOTest extends TestCase
{
    /**
     * Test responsible for checking creations of EventInfoDataDTO objects
     * with all fields filled properly
     * @return void
     */
    public function testCanSetAndGetAllFields(): void
    {
        $dto = new EventInfoDataDTO();

        $name = 'Symfony Conference';
        $description = 'A PHP conference focused on the Symfony framework.';
        $fromDate = new DateTimeImmutable('2025-09-10 09:00');
        $toDate = new DateTimeImmutable('2025-09-12 17:00');
        $availableSeats = 120;

        $dto->setName($name);
        $dto->setDescription($description);
        $dto->setFromDate($fromDate);
        $dto->setToDate($toDate);
        $dto->setAvailableSeats($availableSeats);

        $this->assertEquals($name, $dto->getName());
        $this->assertEquals($description, $dto->getDescription());
        $this->assertSame($fromDate, $dto->getFromDate());
        $this->assertSame($toDate, $dto->getToDate());
        $this->assertEquals($availableSeats, $dto->getAvailableSeats());
    }

    /**
     * Test responsible for checking if description value can be nullable
     * when listing events acton is required by user
     * @return void
     */
    public function testNullableDescriptionCanBeNull(): void
    {
        $dto = new EventInfoDataDTO();

        $dto->setDescription(null);
        $this->assertNull($dto->getDescription());
    }

    /**
     * Test responsible for checking if dates of event are correctly
     * @return void
     */
    public function testFromDateAndToDateAreDateTimeImmutable(): void
    {
        $dto = new EventInfoDataDTO();
        $fromDate = new DateTimeImmutable('2025-01-01');
        $toDate = new DateTimeImmutable('2025-01-02');

        $dto->setFromDate($fromDate);
        $dto->setToDate($toDate);

        $this->assertInstanceOf(DateTimeImmutable::class, $dto->getFromDate());
        $this->assertInstanceOf(DateTimeImmutable::class, $dto->getToDate());
        $this->assertSame('2025-01-01', $dto->getFromDate()->format('Y-m-d'));
        $this->assertSame('2025-01-02', $dto->getToDate()->format('Y-m-d'));
    }
}
