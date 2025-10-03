<?php

namespace App\Tests\Unit\Repository;

use App\DTO\Input\EventCreateDTO;
use App\Manager\ConnectionManager;
use App\Repository\EventRepository;
use DateTimeImmutable;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Test block for checking EventRepository operations
 */
class EventRepositoryTest extends TestCase
{
    /**
     * @var $readPdo
     */
    private $readPdo;

    /**
     * @var $writePdo
     */
    private $writePdo;

    /**
     * @var $cache
     */
    private $cache;

    /**
     * @var $connectionManager
     */
    private $connectionManager;

    /**
     * @var EventRepository
     */
    private EventRepository $repository;

    /**
     * Set an instance of mock object EventRepository
     * with read and write connection manager adapters
     * @return void
     */
    protected function setUp(): void
    {
        $this->readPdo = $this->createMock(PDO::class);
        $this->writePdo = $this->createMock(PDO::class);
        $this->cache = $this->createMock(CacheInterface::class);

        $this->connectionManager = $this->createMock(ConnectionManager::class);
        $this->connectionManager->method('getReadConnection')->willReturn($this->readPdo);
        $this->connectionManager->method('getWriteConnection')->willReturn($this->writePdo);

        $this->repository = new EventRepository($this->cache, $this->connectionManager);
    }

    /**
     * Test responsible for checking if event
     * exist in repository searching by ID
     * @return void
     */
    public function testFindByIdReturnsEvent(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $expected = ['id' => 1, 'name' => 'Evento Test'];

        $this->readPdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM events WHERE id = :id')
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with(['id' => 1]);

        $stmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expected);

        $result = $this->repository->findById(1);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for checking cache event results in redis
     * @return void
     */
    public function testFindFilteredEventsReturnsCachedResults(): void
    {
        $conditions = ['available_seats > 0'];
        $params = [];
        $expected = [['id' => 1, 'name' => 'Evento con asientos']];

        $this->cache->expects($this->once())
            ->method('get')
            ->with(
                $this->stringContains('events_filtered_'),
                $this->isInstanceOf(\Closure::class),
                300
            )
            ->willReturnCallback(function ($key, $callback) {
                $stmt = $this->createMock(PDOStatement::class);
                $stmt->expects($this->once())->method('execute')->with([]);
                $stmt->method('fetchAll')->with(PDO::FETCH_ASSOC)->willReturn([
                    ['id' => 1, 'name' => 'Evento con asientos']
                ]);

                $this->readPdo->method('prepare')->willReturn($stmt);

                return $callback();
            });

        $result = $this->repository->findFilteredEvents($conditions, $params);

        $this->assertSame($expected, $result);
    }

    /**
     * Test for checking updating free seats when booking is created
     * @return void
     */
    public function testUpdateAvailableSeatsAddsSeats(): void
    {
        $stmt = $this->createMock(PDOStatement::class);

        $this->writePdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('available_seats = available_seats + :seats'))
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with(['seats' => 5, 'event_id' => 10])
            ->willReturn(true);

        $result = $this->repository->updateAvailableSeats(10, 5, true);
        $this->assertTrue($result);
    }

    /**
     * Test responsible for checking correct insertions of events in DB
     * @return void
     */
    public function testInsertAddsNewEvent(): void
    {
        $dto = $this->createMock(EventCreateDTO::class);
        $dto->method('getName')->willReturn('Nuevo Evento');
        $dto->method('getDescription')->willReturn('Descripción');
        $dto->method('getFromDate')->willReturn(new DateTimeImmutable('2025-09-01'));
        $dto->method('getToDate')->willReturn(new DateTimeImmutable('2025-09-05'));
        $dto->method('getAvailableSeats')->willReturn(100);

        $stmt = $this->createMock(PDOStatement::class);

        $this->writePdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with([
                'name' => 'Nuevo Evento',
                'description' => 'Descripción',
                'from_date' => '2025-09-01',
                'to_date' => '2025-09-05',
                'available_seats' => 100,
            ])
            ->willReturn(true);

        $this->writePdo->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('123');

        $insertedId = $this->repository->insert($dto);

        $this->assertEquals(123, $insertedId);
    }
}
