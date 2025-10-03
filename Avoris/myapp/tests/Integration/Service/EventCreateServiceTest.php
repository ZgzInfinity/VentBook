<?php

namespace App\Tests\Service;

use App\Assembler\Input\EventCreateDTOAssembler;
use App\Repository\EventRepository;
use App\Service\EventCreateService;
use PHPUnit\Framework\TestCase;
use App\DTO\Input\EventCreateDTO;

/**
 * Test block responsible for creating events according to
 * a specific inout data structure
 */
class EventCreateServiceTest extends TestCase
{
    /**
     * @var $assemblerMock
     */
    private $assemblerMock;

    /**
     * @var $repositoryMock
     */
    private $repositoryMock;

    /**
     * @var EventCreateService
     */
    private EventCreateService $service;

    /**
     * Set an instance of mock object EventCreateService
     * @return void
     */
    protected function setUp(): void
    {
        $this->assemblerMock = $this->createMock(EventCreateDTOAssembler::class);
        $this->repositoryMock = $this->createMock(EventRepository::class);

        $this->service = new EventCreateService(
            $this->assemblerMock,
            $this->repositoryMock
        );
    }

    /**
     * Test responsible for checking correct events creation
     * (When a event is created the service returns the last ID in DB table)
     * @return void
     */
    public function testExecuteReturnsInsertedId(): void
    {
        $params = ['name' => 'Concert', 'date' => '2025-10-01'];
        $fakeDTO = new EventCreateDTO();

        $this->assemblerMock
            ->expects($this->once())
            ->method('transform')
            ->with($params)
            ->willReturn($fakeDTO);

        $this->repositoryMock
            ->expects($this->once())
            ->method('insert')
            ->with($fakeDTO)
            ->willReturn(123);

        $result = $this->service->execute($params);

        $this->assertEquals(123, $result);
    }

    /**
     * Test responsible for checking the possible reporting errors
     * of the input assembler when the data structure is not correct
     * @return void
     */
    public function testExecutePropagatesExceptions(): void
    {
        $this->assemblerMock
            ->method('transform')
            ->willThrowException(new \RuntimeException('Assembler error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Assembler error');

        $this->service->execute(['name' => 'Concert']);
    }
}
