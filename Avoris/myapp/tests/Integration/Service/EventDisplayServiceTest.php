<?php

namespace App\Tests\Integration\Service;

use App\Assembler\Input\EventDisplayDTOAssembler;
use App\Assembler\Output\EventInfoDataDTOAssembler;
use App\DTO\Input\EventDisplayDTO;
use App\DTO\Output\EventInfoDataDTO;
use App\Repository\EventRepository;
use App\Service\EventDisplayService;
use PHPUnit\Framework\TestCase;

/**
 * Test block for checking event detail display
 */
class EventDisplayServiceTest extends TestCase
{
    /**
     * @var $eventRepository
     */
    private $eventRepository;

    /**
     * @var $inputDTOAssembler
     */
    private $inputDTOAssembler;

    /**
     * @var $outputDTOAssembler
     */
    private $outputDTOAssembler;

    /**
     * @var $service
     */
    private $service;

    /**
     * Set an instance of mock object EventDisplayService
     * @return void
     */
    protected function setUp(): void
    {
        $this->eventRepository = $this->createMock(EventRepository::class);
        $this->inputDTOAssembler = $this->createMock(EventDisplayDTOAssembler::class);
        $this->outputDTOAssembler = $this->createMock(EventInfoDataDTOAssembler::class);

        $this->service = new EventDisplayService(
            $this->eventRepository,
            $this->inputDTOAssembler,
            $this->outputDTOAssembler
        );
    }

    /**
     * Test responsible for checking return of event detail in correct data flux
     * @return void
     * @throws \ReflectionException
     */
    public function testExecuteReturnsEventInfoDTOWhenEventFound(): void
    {
        $params = ['id' => 123];

        $dto = new EventDisplayDTO();
        $ref = new \ReflectionProperty($dto, 'id');
        $ref->setAccessible(true);
        $ref->setValue($dto, 123);

        $eventData = ['id' => 123, 'name' => 'Test Event'];
        $outputDTO = $this->createMock(EventInfoDataDTO::class);

        $this->inputDTOAssembler
            ->expects($this->once())
            ->method('transform')
            ->with($params)
            ->willReturn($dto);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with(123)
            ->willReturn($eventData);

        $this->outputDTOAssembler
            ->expects($this->once())
            ->method('transform')
            ->with($eventData)
            ->willReturn($outputDTO);

        $result = $this->service->execute($params);

        $this->assertSame($outputDTO, $result);
    }

    /**
     * Test responsible for checking not found event when it does not exist
     * @return void
     * @throws \ReflectionException
     */
    public function testExecuteReturnsNullWhenEventNotFound(): void
    {
        $params = ['id' => 123];

        $dto = new EventDisplayDTO();
        $ref = new \ReflectionProperty($dto, 'id');
        $ref->setAccessible(true);
        $ref->setValue($dto, 123);

        $this->inputDTOAssembler
            ->expects($this->once())
            ->method('transform')
            ->with($params)
            ->willReturn($dto);

        $this->eventRepository
            ->expects($this->once())
            ->method('findById')
            ->with(123)
            ->willReturn(null);

        $this->outputDTOAssembler
            ->expects($this->never())
            ->method('transform');

        $result = $this->service->execute($params);

        $this->assertNull($result);
    }
}
