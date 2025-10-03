<?php

namespace App\Tests\Integration\Service;

use App\Assembler\Input\EventOptionalFilterDisplayDTOAssembler;
use App\Assembler\Output\EventInfoDataDTOAssembler;
use App\Repository\EventRepository;
use App\Service\EventOptionalFilterDisplayService;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use App\DTO\Output\EventInfoDataDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Test block for checking event optional filter searching
 */
class EventOptionalFilterDisplayServiceTest extends TestCase
{
    /**
     * @var EventRepository
     */
    private EventRepository $eventRepository;

    /**
     * @var EventOptionalFilterDisplayDTOAssembler
     */
    private EventOptionalFilterDisplayDTOAssembler $inputDTOAssembler;

    /**
     * @var EventInfoDataDTOAssembler
     */
    private EventInfoDataDTOAssembler $outputDTOAssembler;

    /**
     * @var EventOptionalFilterDisplayService
     */
    private EventOptionalFilterDisplayService $service;

    /**
     * Set an instance mock object EventOptionalFilterDisplayService
     * @return void
     */
    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepository::class);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        $this->inputDTOAssembler = new EventOptionalFilterDisplayDTOAssembler($validator);
        $this->outputDTOAssembler = new EventInfoDataDTOAssembler();

        $this->service = new EventOptionalFilterDisplayService(
            $this->eventRepository,
            $this->inputDTOAssembler,
            $this->outputDTOAssembler
        );
    }

    /**
     * Test responsible for checking event list according to searching filter params
     * @return void
     */
    public function testExecuteReturnsListOfDTOs(): void
    {
        $params = [
            'filters' => [
                ['EVENT_FILTER_NAME' => 'Music'],
                ['EVENT_FILTER_AVAILABLE' => 10]
            ]
        ];

        // Salida simulada del repositorio (igual que en producción)
        $eventData = [
            [
                'name' => 'Music Fest',
                'from_date' => '2025-08-20',
                'to_date' => '2025-08-21',
                'available_seats' => 50,
                'description' => 'Live concert series'
            ]
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('findFilteredEvents')
            ->with(
                $this->arrayHasKey(0),
                $this->arrayHasKey('name')
            )
            ->willReturn($eventData);

        $result = $this->service->execute($params);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(EventInfoDataDTO::class, $result[0]);
        $this->assertEquals('Music Fest', $result[0]->getName());
        $this->assertEquals(new DateTimeImmutable('2025-08-20'), $result[0]->getFromDate());
        $this->assertEquals(50, $result[0]->getAvailableSeats());
    }


    /**
     * Test responsible for checking empty list result
     * for a concrete filter value
     * @return void
     */
    public function testExecuteReturnsNullWhenNoEvents(): void
    {
        $params = [
            'filters' => [
                ['EVENT_FILTER_NAME' => 'Nonexistent']
            ]
        ];

        $this->eventRepository
            ->expects($this->once())
            ->method('findFilteredEvents')
            ->willReturn([]);

        $result = $this->service->execute($params);

        $this->assertNull($result);
    }

    /**
     * Test responsible for checking invalid filter param value
     * @return void
     */
    public function testExecuteThrowsExceptionForInvalidFilter(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The filter INVALID_FILTER is not available');

        $params = [
            'filters' => [
                ['INVALID_FILTER' => 'x']
            ]
        ];

        $this->service->execute($params);
    }

    /**
     * Test responsible for checking empty filter param value
     * @return void
     */
    public function testExecuteThrowsExceptionForEmptyFilterValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The filter EVENT_FILTER_NAME has not value');

        $params = [
            'filters' => [
                ['EVENT_FILTER_NAME' => '']
            ]
        ];

        $this->service->execute($params);
    }
}
